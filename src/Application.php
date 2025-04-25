<?php

declare(strict_types=1);

namespace Boson;

use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Event\ApplicationStarted;
use Boson\Event\ApplicationStarting;
use Boson\Event\ApplicationStopped;
use Boson\Event\ApplicationStopping;
use Boson\Exception\NoDefaultWindowException;
use Boson\Http\Method\MemoizedMethodFactory;
use Boson\Http\Method\MethodFactory;
use Boson\Http\Method\MethodFactoryInterface;
use Boson\Http\Uri\Factory\MemoizedUriFactory;
use Boson\Http\Uri\Factory\UriFactory;
use Boson\Http\Uri\Factory\UriFactoryInterface;
use Boson\Internal\DebugEnvResolver;
use Boson\Internal\DeferRunner\DeferRunnerInterface;
use Boson\Internal\DeferRunner\NativeShutdownFunctionRunner;
use Boson\Internal\ProcessUnlockPlaceholder;
use Boson\Internal\QuitHandler\PcntlQuitHandler;
use Boson\Internal\QuitHandler\QuitHandlerInterface;
use Boson\Internal\QuitHandler\WindowsQuitHandler;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\ThreadsCountResolver;
use Boson\Shared\Marker\BlockingOperation;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\WebView;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Manager\WindowManager;
use Boson\Window\Window;
use FFI\CData;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * @api
 */
final class Application
{
    /**
     * Unique application identifier.
     *
     * It is worth noting that the destruction of this object
     * from memory (deallocation using PHP GC) means the physical
     * destruction of all data associated with it, including unmanaged.
     */
    public readonly ApplicationId $id;

    /**
     * Gets windows list and methods for working with windows.
     */
    public readonly WindowManager $windows;

    /**
     * Gets access to the listener of the window events
     * and intention subscriptions.
     */
    public readonly EventListener $events;

    /**
     * Provides more convenient and faster access to the
     * {@see WindowManager::$default} subsystem from
     * child {@see $windows} property.
     *
     * @uses WindowManager::$default Default (first) window of the windows list
     */
    public Window $window {
        /**
         * Gets the default window of the application.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->windows->default
            ?? throw NoDefaultWindowException::becauseNoDefaultWindow();
    }

    /**
     * Provides more convenient and faster access to the {@see Window::$webview}
     * subsystem from {@see $window} property.
     *
     * @uses Window::$webview The webview of the default (first) window
     */
    public WebView $webview {
        /**
         * Gets the WebView instance associated with the default window.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->window->webview;
    }

    /**
     * Gets debug mode of an application.
     *
     * Unlike {@see ApplicationCreateInfo::$debug}, it contains
     * a REAL debug value, including possibly automatically derived.
     *
     * Contains {@see true} in case of debug mode
     * is enabled or {@see false} instead.
     */
    public readonly bool $isDebug;

    /**
     * Gets running state of an application.
     *
     * Contains {@see true} in case of application is running
     * or {@see false} instead.
     */
    public private(set) bool $isRunning = false;

    /**
     * Indicates whether the application was ever running
     */
    private bool $wasEverRunning = false;

    /**
     * Shared WebView API library.
     *
     * @internal Not safe, you can get segfault, use
     *           this low-level API at your own risk!
     */
    public readonly LibSaucer $api;

    /**
     * Gets status of quit handler registration.
     */
    private bool $quitHandlerIsRegistered = false;

    /**
     * Gets status of defer runner registration.
     */
    private bool $deferRunnerIsRegistered = false;

    /**
     * Gets an internal application placeholder to unlock the
     * webview process workflow.
     */
    private readonly ProcessUnlockPlaceholder $placeholder;

    /**
     * Gets an internal URI factory implementation.
     */
    private readonly UriFactoryInterface $uris;

    /**
     * Gets an internal HTTP method factory implementation.
     */
    private readonly MethodFactoryInterface $methods;

    /**
     * @param PsrEventDispatcherInterface|null $dispatcher an optional event
     *        dispatcher for handling application events
     */
    public function __construct(
        /**
         * Gets an information DTO about the application
         * with which it was created.
         */
        public readonly ApplicationCreateInfo $info = new ApplicationCreateInfo(),
        ?PsrEventDispatcherInterface $dispatcher = null,
    ) {
        $this->api = new LibSaucer($this->info->library);
        $this->isDebug = DebugEnvResolver::resolve($this->info->debug);
        $this->events = $this->createEventListener($dispatcher);
        $this->id = $this->createApplicationId($this->info->name, $this->info->threads);

        $this->placeholder = new ProcessUnlockPlaceholder(
            api: $this->api,
            app: $this,
        );

        $this->uris = new MemoizedUriFactory(
            delegate: new UriFactory(),
        );

        $this->methods = new MemoizedMethodFactory(
            delegate: new MethodFactory(),
        );

        $this->registerSchemes();
        $this->registerDefaultEventListeners();

        $this->windows = $this->createWindowManager();

        if ($this->info->autorun) {
            $this->registerDeferRunnerIfNotRegistered();
        }
    }

    /**
     * Creates a new window manager instance.
     *
     * This method initializes and returns a {@see WindowManager} object
     * that is responsible for managing all application windows.
     */
    private function createWindowManager(): WindowManager
    {
        return new WindowManager(
            api: $this->api,
            app: $this,
            placeholder: $this->placeholder,
            uris: $this->uris,
            methods: $this->methods,
            info: $this->info->window,
            dispatcher: $this->events,
        );
    }

    /**
     * Register list of protocol names that will be
     * intercepted by the application.
     */
    private function registerSchemes(): void
    {
        foreach ($this->info->schemes as $scheme) {
            $this->api->saucer_register_scheme($scheme);
        }
    }

    /**
     * Registers default event listeners for the application.
     *
     * This includes handling window close events.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->events->addEventListener(WindowClosed::class, $this->onWindowClose(...));
    }

    /**
     * Handles the window close event.
     *
     * If {@see $quitOnClose} is enabled ({@see true}) and
     * all windows are closed, the application will quit.
     */
    private function onWindowClose(): void
    {
        if ($this->info->quitOnClose && $this->windows->count() === 0) {
            $this->quit();
        }
    }

    /**
     * Creates local (application-aware) event listener
     * based on the provided dispatcher.
     */
    private function createEventListener(?PsrEventDispatcherInterface $dispatcher): EventListener
    {
        if ($dispatcher === null) {
            return new EventListener();
        }

        return new DelegateEventListener($dispatcher);
    }

    /**
     * Creates a new application ID
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    private function createApplicationId(string $name, ?int $threads): ApplicationId
    {
        return ApplicationId::fromAppHandle(
            api: $this->api,
            handle: $this->createApplicationPointer($name, $threads),
            name: $name,
        );
    }

    /**
     * Creates a new application instance pointer.
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    #[RequiresDealloc]
    private function createApplicationPointer(string $name, ?int $threads): CData
    {
        $options = $this->createApplicationOptionsPointer($name, $threads);

        try {
            return $this->api->saucer_application_init($options);
        } finally {
            $this->api->saucer_options_free($options);
        }
    }

    /**
     * Creates a new application options pointer.
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    #[RequiresDealloc]
    private function createApplicationOptionsPointer(string $name, ?int $threads): CData
    {
        $options = $this->api->saucer_options_new($name);

        $threads = ThreadsCountResolver::resolve($threads);
        if ($threads !== null) {
            $this->api->saucer_options_set_threads($options, $threads);
        }

        return $options;
    }

    /**
     * Returns a list of quit handlers (intercepts alternative
     * application shutdown commands) for the application.
     *
     * @return iterable<array-key, QuitHandlerInterface>
     */
    private function getQuitHandlers(): iterable
    {
        yield new WindowsQuitHandler();
        yield new PcntlQuitHandler();
    }

    /**
     * Registers quit handlers if they haven't been registered yet.
     *
     * This ensures that the application can be properly terminated.
     */
    private function registerQuitHandlersIfNotRegistered(): void
    {
        if ($this->quitHandlerIsRegistered) {
            return;
        }

        foreach ($this->getQuitHandlers() as $handler) {
            if ($handler->isSupported === false) {
                continue;
            }

            // Register EVERY quit handler
            $handler->register($this->quit(...));
        }

        $this->quitHandlerIsRegistered = true;
    }

    /**
     * Returns a list of defer runners for the application.
     *
     * @return iterable<array-key, DeferRunnerInterface>
     */
    private function getDeferRunners(): iterable
    {
        yield new NativeShutdownFunctionRunner();
    }

    /**
     * Registers a defer runner if none has been registered yet.
     *
     * This allows the application to be started automatically
     * after script execution.
     */
    private function registerDeferRunnerIfNotRegistered(): void
    {
        if ($this->deferRunnerIsRegistered) {
            return;
        }

        foreach ($this->getDeferRunners() as $runner) {
            if ($runner->isSupported === false) {
                continue;
            }

            // Register FIRST deferred runner
            $runner->register($this->runIfNotEverRunning(...));
            break;
        }

        $this->deferRunnerIsRegistered = true;
    }

    /**
     * Runs the application if it has never been run before.
     *
     * This is used by the defer runner to start
     * the application automatically.
     */
    private function runIfNotEverRunning(): void
    {
        if ($this->wasEverRunning) {
            return;
        }

        $this->run();
    }

    /**
     * Dispatches an intention to launch an application and returns a {@see bool}
     * result: whether to start the application or not.
     */
    private function shouldNotStart(): bool
    {
        $intention = $this->events->dispatch(new ApplicationStarting($this));

        return $intention->isCancelled;
    }

    /**
     * Runs the application, starting the main event loop.
     *
     * This method blocks main thread until the
     * application is quit.
     *
     * @api
     */
    #[BlockingOperation]
    public function run(): void
    {
        if ($this->isRunning || $this->shouldNotStart()) {
            return;
        }

        $this->isRunning = true;
        $this->wasEverRunning = true;

        $this->registerQuitHandlersIfNotRegistered();

        $this->events->dispatch(new ApplicationStarted($this));

        do {
            $this->api->saucer_application_run_once($this->id->ptr);

            \usleep(1);
        } while ($this->isRunning);
    }

    /**
     * Dispatches an intention to stop an application and returns a {@see bool}
     * result: whether to stop the application or not.
     */
    private function shouldNotStop(): bool
    {
        $intention = $this->events->dispatch(new ApplicationStopping($this));

        return $intention->isCancelled;
    }

    /**
     * Quits the application, stopping the main
     * loop and releasing resources.
     *
     * @api
     */
    public function quit(): void
    {
        if ($this->shouldNotStop()) {
            return;
        }

        $this->isRunning = false;
        $this->api->saucer_application_quit($this->id->ptr);

        $this->events->dispatch(new ApplicationStopped($this));
    }

    /**
     * Destructor for the Application class.
     *
     * Ensures that all resources are properly released
     * when the application is destroyed.
     */
    public function __destruct()
    {
        $this->api->saucer_application_quit($this->id->ptr);
        $this->api->saucer_application_free($this->id->ptr);
    }
}
