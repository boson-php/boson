<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

use Boson\Environment\Architecture;
use Boson\Environment\Exception\UnsupportedArchitectureException;
use Boson\Environment\Exception\UnsupportedOperatingSystemException;
use Boson\Environment\OperatingSystem;
use FFI\Env\Runtime;

/**
 * @mixin \FFI
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class LibSaucer
{
    /**
     * @var non-empty-string
     */
    private const string MINIMAL_REQUIRED_VERSION = '0.2.0';

    /**
     * @var non-empty-string
     */
    private const string MAXIMAL_SUPPORTED_VERSION = '1.0.0';

    /**
     * @var non-empty-string
     */
    private const string DEFAULT_BIN_DIR = __DIR__ . '/../../../bin';

    private \FFI $ffi;

    /**
     * @param non-empty-string|null $library
     */
    public function __construct(
        ?string $library = null,
    ) {
        Runtime::assertAvailable();

        $this->ffi = \FFI::cdef(
            code: (string) \file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
            lib: $library ?? match ($os = OperatingSystem::current()) {
                OperatingSystem::Windows => match ($arch = Architecture::current()) {
                    Architecture::x86,
                    Architecture::Amd64 => self::DEFAULT_BIN_DIR . '/libboson-windows-x86_64.dll',
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid($arch->name),
                },
                OperatingSystem::Linux => match ($arch = Architecture::current()) {
                    Architecture::x86,
                    Architecture::Amd64 => self::DEFAULT_BIN_DIR . '/libboson-linux-x86_64.so',
                    Architecture::Arm64 => self::DEFAULT_BIN_DIR . '/libboson-linux-aarch64.so',
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid($arch->name),
                },
                OperatingSystem::MacOS => match ($arch = Architecture::current()) {
                    Architecture::x86,
                    Architecture::Amd64,
                    Architecture::Arm64 => self::DEFAULT_BIN_DIR . '/libboson-darwin-universal.dylib',
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid($arch->name),
                },
                default => throw UnsupportedOperatingSystemException::becauseOperatingSystemIsInvalid($os->name),
            },
        );

        $this->assertVersionCompatibility();
    }

    private function assertVersionCompatibility(): void
    {
        /** @var string $version */
        $version = $this->ffi->boson_version();

        $isSupported = \version_compare($version, self::MINIMAL_REQUIRED_VERSION, '>=')
            && \version_compare($version, self::MAXIMAL_SUPPORTED_VERSION, '<');

        if ($isSupported) {
            return;
        }

        throw new \OutOfRangeException(\sprintf(
            'The current version of the library is %s, '
                . 'but at least %s (but not higher than %s) is supported',
            $version,
            self::MINIMAL_REQUIRED_VERSION,
            self::MAXIMAL_SUPPORTED_VERSION,
        ));
    }

    /**
     * @param non-empty-string $method
     * @param array<non-empty-string|int<0, max>, mixed> $args
     */
    public function __call(string $method, array $args): mixed
    {
        assert($method !== '', 'Method name MUST not be empty');

        return $this->ffi->$method(...$args);
    }

    public function __serialize(): array
    {
        throw new \LogicException('Cannot serialize library');
    }

    public function __clone()
    {
        throw new \LogicException('Cannot clone library');
    }
}

__halt_compiler();


/// Generic Build Functions

const char* boson_version();

/// Saucer Data Types

// .options
typedef struct saucer_options saucer_options;

// .app
typedef struct saucer_application saucer_application;
typedef void (*saucer_pool_callback)();
typedef void (*saucer_post_callback)();

// .stash
typedef struct saucer_stash saucer_stash;
typedef saucer_stash *(*saucer_stash_lazy_callback)();

// .icon
typedef struct saucer_icon saucer_icon;

// .navigation
typedef struct saucer_navigation saucer_navigation;

// .preferences
typedef struct saucer_preferences saucer_preferences;

// .window
typedef enum SAUCER_WINDOW_EVENT
{
    SAUCER_WINDOW_EVENT_DECORATED,
    SAUCER_WINDOW_EVENT_MAXIMIZE,
    SAUCER_WINDOW_EVENT_MINIMIZE,
    SAUCER_WINDOW_EVENT_CLOSED,
    SAUCER_WINDOW_EVENT_RESIZE,
    SAUCER_WINDOW_EVENT_FOCUS,
    SAUCER_WINDOW_EVENT_CLOSE,
} SAUCER_WINDOW_EVENT;

typedef enum SAUCER_WINDOW_EDGE
{
    SAUCER_WINDOW_EDGE_TOP = 1 << 0,
    SAUCER_WINDOW_EDGE_BOTTOM = 1 << 1,
    SAUCER_WINDOW_EDGE_LEFT = 1 << 2,
    SAUCER_WINDOW_EDGE_RIGHT = 1 << 3,
} SAUCER_WINDOW_EDGE;

typedef enum SAUCER_POLICY
{
    SAUCER_POLICY_ALLOW,
    SAUCER_POLICY_BLOCK,
} SAUCER_POLICY;

typedef struct saucer_handle saucer_handle;

// .scheme
typedef enum SAUCER_SCHEME_ERROR
{
    SAUCER_REQUEST_ERROR_NOT_FOUND,
    SAUCER_REQUEST_ERROR_INVALID,
    SAUCER_REQUEST_ERROR_ABORTED,
    SAUCER_REQUEST_ERROR_DENIED,
    SAUCER_REQUEST_ERROR_FAILED,
} SAUCER_SCHEME_ERROR;

typedef struct saucer_scheme_response saucer_scheme_response;
typedef struct saucer_scheme_request saucer_scheme_request;
typedef struct saucer_scheme_executor saucer_scheme_executor;

typedef void (*saucer_scheme_handler)(saucer_handle *, saucer_scheme_request *, saucer_scheme_executor *);

// .script
typedef enum SAUCER_LOAD_TIME
{
    SAUCER_LOAD_TIME_CREATION,
    SAUCER_LOAD_TIME_READY,
} SAUCER_LOAD_TIME;

typedef enum SAUCER_WEB_FRAME
{
    SAUCER_WEB_FRAME_TOP,
    SAUCER_WEB_FRAME_ALL,
} SAUCER_WEB_FRAME;

typedef struct saucer_script saucer_script;

// .webview
typedef enum SAUCER_WEB_EVENT
{
    SAUCER_WEB_EVENT_DOM_READY,
    SAUCER_WEB_EVENT_NAVIGATED,
    SAUCER_WEB_EVENT_NAVIGATE,
    SAUCER_WEB_EVENT_FAVICON,
    SAUCER_WEB_EVENT_TITLE,
    SAUCER_WEB_EVENT_LOAD,
} SAUCER_WEB_EVENT;

typedef enum SAUCER_STATE
{
    SAUCER_STATE_STARTED,
    SAUCER_STATE_FINISHED,
} SAUCER_STATE;

typedef enum SAUCER_LAUNCH
{
    SAUCER_LAUNCH_SYNC,
    SAUCER_LAUNCH_ASYNC,
} SAUCER_LAUNCH;

typedef struct saucer_embedded_file saucer_embedded_file;

typedef bool (*saucer_on_message)(const char *);

/// Saucer Functions

// .options
saucer_options *saucer_options_new(const char *id);
void saucer_options_free(saucer_options *);

void saucer_options_set_argc(saucer_options *, int argc);
void saucer_options_set_argv(saucer_options *, char **argv);

void saucer_options_set_threads(saucer_options *, size_t threads);

// .app
saucer_application *saucer_application_init(saucer_options *options);
void saucer_application_free(saucer_application *);
saucer_application *saucer_application_active();
bool saucer_application_thread_safe(saucer_application *);

/**
 * @brief Submits (blocking) the given @param callback to the thread-pool
 */
void saucer_application_pool_submit(saucer_application *, saucer_pool_callback callback);

/**
 * @brief Emplaces (non blocking) the given @param callback to the thread-pool
 */
void saucer_application_pool_emplace(saucer_application *, saucer_pool_callback callback);
void saucer_application_post(saucer_application *, saucer_post_callback callback);
void saucer_application_quit(saucer_application *);
void saucer_application_run(saucer_application *);
void saucer_application_run_once(saucer_application *);

// .stash
void saucer_stash_free(saucer_stash *);
size_t saucer_stash_size(saucer_stash *);
const uint8_t *saucer_stash_data(saucer_stash *);
saucer_stash *saucer_stash_from(const uint8_t *data, size_t size);
saucer_stash *saucer_stash_view(const uint8_t *data, size_t size);

/**
 * @note The stash returned from within the @param callback is automatically
 *       deleted. However, the stash returned from this function must still
 *       be free'd accordingly.
 */
saucer_stash *saucer_stash_lazy(saucer_stash_lazy_callback callback);

// .icon
void saucer_icon_free(saucer_icon *);
bool saucer_icon_empty(saucer_icon *);
saucer_stash *saucer_icon_data(saucer_icon *);
void saucer_icon_save(saucer_icon *, const char *path);

/**
 * @brief Try to construct an icon from a given file.
 * @note The pointer pointed to by @param result will be set to a saucer_icon
 *       in case of success. The returned icon must be free'd.
 */
void saucer_icon_from_file(saucer_icon **result, const char *file);

/**
 * @brief Try to construct an icon from a given stash (raw bytes).
 * @note The pointer pointed to by @param result will be set to a saucer_icon
 *       in case of success. The returned icon must be free'd.
 */
void saucer_icon_from_data(saucer_icon **result, saucer_stash *stash);

// .memory
void saucer_memory_free(void *data);
void *saucer_memory_alloc(size_t size);

// .navigation
void saucer_navigation_free(saucer_navigation *);
/*[[sc::requires_free]]*/ char *saucer_navigation_url(saucer_navigation *);
bool saucer_navigation_new_window(saucer_navigation *);
bool saucer_navigation_redirection(saucer_navigation *);
bool saucer_navigation_user_initiated(saucer_navigation *);

// .preferences
saucer_preferences *saucer_preferences_new(saucer_application *app);
void saucer_preferences_free(saucer_preferences *);
void saucer_preferences_set_persistent_cookies(saucer_preferences *, bool enabled);
void saucer_preferences_set_hardware_acceleration(saucer_preferences *, bool enabled);
void saucer_preferences_set_storage_path(saucer_preferences *, const char *path);
void saucer_preferences_add_browser_flag(saucer_preferences *, const char *flag);
void saucer_preferences_set_user_agent(saucer_preferences *, const char *user_agent);

// .window
bool saucer_window_visible(saucer_handle *);
bool saucer_window_focused(saucer_handle *);
bool saucer_window_minimized(saucer_handle *);
bool saucer_window_maximized(saucer_handle *);
bool saucer_window_resizable(saucer_handle *);
bool saucer_window_decorations(saucer_handle *);
bool saucer_window_always_on_top(saucer_handle *);
bool saucer_window_click_through(saucer_handle *);
/*[[sc::requires_free]]*/ char *saucer_window_title(saucer_handle *);
void saucer_window_size(saucer_handle *, int *width, int *height);
void saucer_window_max_size(saucer_handle *, int *width, int *height);
void saucer_window_min_size(saucer_handle *, int *width, int *height);
void saucer_window_hide(saucer_handle *);
void saucer_window_show(saucer_handle *);
void saucer_window_close(saucer_handle *);
void saucer_window_focus(saucer_handle *);
void saucer_window_start_drag(saucer_handle *);
void saucer_window_start_resize(saucer_handle *, SAUCER_WINDOW_EDGE edge);
void saucer_window_set_minimized(saucer_handle *, bool enabled);
void saucer_window_set_maximized(saucer_handle *, bool enabled);
void saucer_window_set_resizable(saucer_handle *, bool enabled);
void saucer_window_set_decorations(saucer_handle *, bool enabled);
void saucer_window_set_always_on_top(saucer_handle *, bool enabled);
void saucer_window_set_click_through(saucer_handle *, bool enabled);
void saucer_window_set_icon(saucer_handle *, saucer_icon *icon);
void saucer_window_set_title(saucer_handle *, const char *title);
void saucer_window_set_size(saucer_handle *, int width, int height);
void saucer_window_set_max_size(saucer_handle *, int width, int height);
void saucer_window_set_min_size(saucer_handle *, int width, int height);
void saucer_window_clear(saucer_handle *, SAUCER_WINDOW_EVENT event);
void saucer_window_remove(saucer_handle *, SAUCER_WINDOW_EVENT event, uint64_t id);

/**
 * @note See respective comment on events in "window.h"
 * @warning The icon passed to the @param callback in the `web_event::icon_changed` event must be free'd by the
 * user
 */
void saucer_window_once(saucer_handle *, SAUCER_WINDOW_EVENT event, void *callback);
uint64_t saucer_window_on(saucer_handle *, SAUCER_WINDOW_EVENT event, void *callback);

// .scheme
saucer_scheme_response *saucer_scheme_response_new(saucer_stash *data, const char *mime);
void saucer_scheme_response_free(saucer_scheme_response *);
void saucer_scheme_response_set_status(saucer_scheme_response *, int status);
void saucer_scheme_response_add_header(saucer_scheme_response *, const char *header, const char *value);
// @see https://github.com/saucer/bindings/pull/2
// void saucer_scheme_request_free(saucer_scheme_request *);
/*[[sc::requires_free]]*/ char *saucer_scheme_request_url(saucer_scheme_request *);
/*[[sc::requires_free]]*/ char *saucer_scheme_request_method(saucer_scheme_request *);
/*[[sc::requires_free]]*/ saucer_stash *saucer_scheme_request_content(saucer_scheme_request *);

/**
 * @note The arrays pointed to by @param headers and @param values will be populated with strings which are
 * themselves dynamically allocated. Both arrays will then hold @param count elements.
 *
 * To properly free the returned arrays you should:
 * - Free all strings within the headers and values array
 * - Free the array itself
 */
void saucer_scheme_request_headers(saucer_scheme_request *, char ***headers, char ***values, size_t *count);
void saucer_scheme_executor_free(saucer_scheme_executor *);
void saucer_scheme_executor_resolve(saucer_scheme_executor *, saucer_scheme_response *response);
void saucer_scheme_executor_reject(saucer_scheme_executor *, SAUCER_SCHEME_ERROR error);

// .script
saucer_script *saucer_script_new(const char *code, SAUCER_LOAD_TIME time);
void saucer_script_free(saucer_script *);

void saucer_script_set_frame(saucer_script *, SAUCER_WEB_FRAME frame);
void saucer_script_set_time(saucer_script *, SAUCER_LOAD_TIME time);
void saucer_script_set_permanent(saucer_script *, bool permanent);
void saucer_script_set_code(saucer_script *, const char *code);

// .webview

/*[[sc::requires_free]]*/ saucer_embedded_file *saucer_embed(saucer_stash *content, const char *mime);
void saucer_embed_free(saucer_embedded_file *);
/*[[sc::requires_free]]*/ saucer_handle *saucer_new(saucer_preferences *prefs);
void saucer_free(saucer_handle *);
void saucer_webview_on_message(saucer_handle *, saucer_on_message callback);
/*[[sc::requires_free]]*/ saucer_icon *saucer_webview_favicon(saucer_handle *);
/*[[sc::requires_free]]*/ char *saucer_webview_page_title(saucer_handle *);
bool saucer_webview_dev_tools(saucer_handle *);
/*[[sc::requires_free]]*/ char *saucer_webview_url(saucer_handle *);
bool saucer_webview_context_menu(saucer_handle *);
void saucer_webview_background(saucer_handle *, uint8_t *r, uint8_t *g, uint8_t *b, uint8_t *a);
bool saucer_webview_force_dark_mode(saucer_handle *);
void saucer_webview_set_dev_tools(saucer_handle *, bool enabled);
void saucer_webview_set_context_menu(saucer_handle *, bool enabled);
void saucer_webview_set_force_dark_mode(saucer_handle *, bool enabled);
void saucer_webview_set_background(saucer_handle *, uint8_t r, uint8_t g, uint8_t b, uint8_t a);
void saucer_webview_set_file(saucer_handle *, const char *file);
void saucer_webview_set_url(saucer_handle *, const char *url);
void saucer_webview_back(saucer_handle *);
void saucer_webview_forward(saucer_handle *);
void saucer_webview_reload(saucer_handle *);
void saucer_webview_embed_file(saucer_handle *, const char *name, saucer_embedded_file *file, SAUCER_LAUNCH policy);
void saucer_webview_serve(saucer_handle *, const char *file);
void saucer_webview_clear_scripts(saucer_handle *);
void saucer_webview_clear_embedded(saucer_handle *);
void saucer_webview_clear_embedded_file(saucer_handle *, const char *file);
void saucer_webview_inject(saucer_handle *, saucer_script *script);
void saucer_webview_execute(saucer_handle *, const char *code);
void saucer_webview_handle_scheme(saucer_handle *, const char *name, saucer_scheme_handler handler, SAUCER_LAUNCH policy);
void saucer_webview_remove_scheme(saucer_handle *, const char *name);
void saucer_webview_clear(saucer_handle *, SAUCER_WEB_EVENT event);
void saucer_webview_remove(saucer_handle *, SAUCER_WEB_EVENT event, uint64_t id);

/**
 * @note The @param callback should be a function pointer to a function matching the event, that is:
 * <return-type>(saucer_handle *, <params>...);
 *
 * Where "<return-type>" and "<params>..." are to be substituted according
 * to the given event signature (see the respective C++ header)
 *
 * @example web_event::title_changed => void(*)(saucer_handle *, const char *)
 */
void saucer_webview_once(saucer_handle *, SAUCER_WEB_EVENT event, void *callback);
uint64_t saucer_webview_on(saucer_handle *, SAUCER_WEB_EVENT event, void *callback);
/*[[sc::before_init]]*/ void saucer_register_scheme(const char *name);
