<?php

declare(strict_types=1);

namespace Boson\Tests\Stub;

use Boson\Application;
use Boson\Component\Saucer\SaucerTestingStub;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\LifecycleEvents\LifecycleEventsListener;
use Boson\Window\Internal\SaucerWindowEventHandler;
use FFI\CData;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Tests
 */
class TestingApplicationStub extends Application
{
    #[\Override]
    protected function createLibSaucer(?string $library): SaucerTestingStub
    {
        $stub = new SaucerTestingStub();

        $stub->addDefaultMethod('cast', fn(string $t, CData $ptr) => $ptr);
        $stub->addDefaultMethod('new', $this->createStruct(...));

        $stub->addDefaultMethod('saucer_application_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_application_new', $args));

        $stub->addDefaultMethod('saucer_application_options_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_application_options_new', $args));

        $stub->addDefaultMethod('saucer_loop_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_loop_new', $args));

        $stub->addDefaultMethod('saucer_desktop_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_desktop_new', $args));

        $stub->addDefaultMethod('saucer_window_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_window_new', $args));

        $stub->addDefaultMethod('saucer_webview_options_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_webview_options_new', $args));

        $stub->addDefaultMethod('saucer_webview_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_webview_new', $args));

        $stub->addDefaultMethod('saucer_script_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_script_new', $args));

        $stub->addDefaultMethod('saucer_webview_options_append_browser_flag');
        $stub->addDefaultMethod('saucer_webview_options_set_persistent_cookies');
        $stub->addDefaultMethod('saucer_webview_options_set_hardware_acceleration');

        $stub->addDefaultMethod('saucer_loop_iteration');

        $stub->addDefaultMethod('saucer_window_set_size');
        $stub->addDefaultMethod('saucer_window_set_title');
        $stub->addDefaultMethod('saucer_window_set_resizable');
        $stub->addDefaultMethod('saucer_window_set_decorations');
        $stub->addDefaultMethod('saucer_window_on', static fn(): int => 0xDEAD_BEEF);
        $stub->addDefaultMethod('saucer_window_off');
        $stub->addDefaultMethod('saucer_window_show');
        $stub->addDefaultMethod('saucer_window_set_always_on_top');
        $stub->addDefaultMethod('saucer_window_set_click_through');

        $stub->addDefaultMethod('saucer_webview_set_context_menu');
        $stub->addDefaultMethod('saucer_webview_set_dev_tools');
        $stub->addDefaultMethod('saucer_webview_inject', static fn(): int => 0xDEAD_BEEF);
        $stub->addDefaultMethod('saucer_webview_on', static fn(): int => 0xDEAD_BEEF);
        $stub->addDefaultMethod('saucer_webview_background');
        $stub->addDefaultMethod('saucer_webview_set_force_dark');
        $stub->addDefaultMethod('saucer_webview_set_background');

        $stub->addDefaultMethod('saucer_script_set_permanent');

        // cleanup

        $stub->addDefaultMethod('saucer_application_options_free');
        $stub->addDefaultMethod('saucer_desktop_free');
        $stub->addDefaultMethod('saucer_loop_free');
        $stub->addDefaultMethod('saucer_window_free');
        $stub->addDefaultMethod('saucer_webview_options_free');
        $stub->addDefaultMethod('saucer_application_quit');
        $stub->addDefaultMethod('saucer_webview_uninject_all');
        $stub->addDefaultMethod('saucer_webview_uninject');

        return $stub;
    }

    #[\Override]
    #[BlockingOperation]
    public function run(): void
    {
        $this->poller->defer(function () {
            $this->quit();
        });

        parent::run();
    }

    /**
     * @param non-empty-string $type
     * @param array<array-key, mixed> $args
     */
    protected function createStruct(string $type, array $args = []): CData
    {
        return match (true) {
            $this->isWebViewEventsStruct($type) => \FFI::cdef(<<<'C'
                    typedef void* saucer_webview;
                    typedef void* saucer_url;
                    typedef void* saucer_navigation;
                    typedef void* saucer_icon;

                    typedef int32_t SAUCER_POLICY;
                    typedef int32_t SAUCER_STATE;
                    C)
                ->new($type),
            $this->isWindowEventsStruct($type) => \FFI::cdef(<<<'C'
                    typedef void* saucer_window;

                    typedef int32_t SAUCER_POLICY;
                    typedef int32_t SAUCER_WINDOW_DECORATION;
                    C)
                ->new($type),
            default => \FFI::cdef()
                ->new('int64_t'),
        };
    }

    private function isWebViewEventsStruct(string $type): bool
    {
        return new \ReflectionClassConstant(LifecycleEventsListener::class, 'WEBVIEW_HANDLER_STRUCT')
            ->getValue() === $type;
    }

    private function isWindowEventsStruct(string $type): bool
    {
        return new \ReflectionClassConstant(SaucerWindowEventHandler::class, 'WINDOW_HANDLER_STRUCT')
                ->getValue() === $type;
    }
}
