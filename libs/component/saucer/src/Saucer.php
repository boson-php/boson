<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

use Boson\Component\Saucer\Exception\InvalidLibraryException;
use Boson\Component\Saucer\Loader\CpuArchitecture;
use Boson\Component\Saucer\Loader\DesktopEnvironment;
use Boson\Component\Saucer\Loader\LibraryDetector;
use Boson\Component\Saucer\Loader\OperatingSystem;
use Boson\Component\Saucer\Validator\VersionChecker;
use FFI\Env\Runtime;

final readonly class Saucer implements SaucerInterface
{
    private \FFI $ffi;

    /**
     * @param non-empty-string $library
     */
    public function __construct(string $library, bool $validateVersion = true)
    {
        Runtime::assertAvailable();

        if (!\is_file($library)) {
            throw InvalidLibraryException::becauseFileNotFound($library);
        }

        $this->ffi = \FFI::cdef(
            code: (string) \file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
            lib: $library,
        );

        if ($validateVersion) {
            VersionChecker::check($this->ffi);
        }
    }

    public static function createFromGlobals(): self
    {
        return self::createFromEnvironment();
    }

    public static function createFromEnvironment(
        ?OperatingSystem $os = null,
        ?CpuArchitecture $arch = null,
        ?DesktopEnvironment $de = null,
    ): self {
        return self::createWithLibraryDetector(new LibraryDetector($os, $arch, $de));
    }

    public static function createWithLibraryDetector(LibraryDetector $detector): self
    {
        return new self((string) $detector);
    }

    /**
     * @param non-empty-string $name
     */
    public function __get(string $name): mixed
    {
        return $this->ffi->$name;
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

const char *boson_version();

/// Saucer Data Types

// .app
typedef struct saucer_screen saucer_screen;
typedef struct saucer_application saucer_application;
typedef struct saucer_application_options saucer_application_options;

typedef enum SAUCER_POLICY
{
    SAUCER_POLICY_ALLOW,
    SAUCER_POLICY_BLOCK,
} SAUCER_POLICY;

typedef enum SAUCER_APPLICATION_EVENT
{
    SAUCER_APPLICATION_EVENT_QUIT,
} SAUCER_APPLICATION_EVENT;

typedef SAUCER_POLICY (*saucer_application_event_quit)(saucer_application *, void *);

typedef void (*saucer_post_callback)(void *);
typedef void (*saucer_run_callback)(saucer_application *, void *);
typedef void (*saucer_finish_callback)(saucer_application *, void *);

// .stash
typedef struct saucer_stash saucer_stash;
typedef saucer_stash *(*saucer_stash_lazy_callback)(void *);

// .icon
typedef struct saucer_icon saucer_icon;

// .window
typedef struct saucer_window saucer_window;

typedef enum SAUCER_WINDOW_EDGE
{
    SAUCER_WINDOW_EDGE_TOP          = 1 << 0,
    SAUCER_WINDOW_EDGE_BOTTOM       = 1 << 1,
    SAUCER_WINDOW_EDGE_LEFT         = 1 << 2,
    SAUCER_WINDOW_EDGE_RIGHT        = 1 << 3,
    SAUCER_WINDOW_EDGE_BOTTOM_LEFT  = SAUCER_WINDOW_EDGE_BOTTOM | SAUCER_WINDOW_EDGE_LEFT,
    SAUCER_WINDOW_EDGE_BOTTOM_RIGHT = SAUCER_WINDOW_EDGE_BOTTOM | SAUCER_WINDOW_EDGE_RIGHT,
    SAUCER_WINDOW_EDGE_TOP_LEFT     = SAUCER_WINDOW_EDGE_TOP | SAUCER_WINDOW_EDGE_LEFT,
    SAUCER_WINDOW_EDGE_TOP_RIGHT    = SAUCER_WINDOW_EDGE_TOP | SAUCER_WINDOW_EDGE_RIGHT,
} SAUCER_WINDOW_EDGE;

typedef enum SAUCER_WINDOW_DECORATION
{
    SAUCER_WINDOW_DECORATION_NONE,
    SAUCER_WINDOW_DECORATION_PARTIAL,
    SAUCER_WINDOW_DECORATION_FULL,
} SAUCER_WINDOW_DECORATION;

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

typedef void (*saucer_window_event_decorated)(saucer_window *, SAUCER_WINDOW_DECORATION, void *);
typedef void (*saucer_window_event_maximize)(saucer_window *, bool, void *);
typedef void (*saucer_window_event_minimize)(saucer_window *, bool, void *);
typedef void (*saucer_window_event_closed)(saucer_window *, void *);
typedef void (*saucer_window_event_resize)(saucer_window *, int, int, void *);
typedef void (*saucer_window_event_focus)(saucer_window *, bool, void *);
typedef SAUCER_POLICY (*saucer_window_event_close)(saucer_window *, void *);

// .url
typedef struct saucer_url saucer_url;

// .scheme
typedef struct saucer_scheme_executor saucer_scheme_executor;
typedef struct saucer_scheme_request saucer_scheme_request;
typedef struct saucer_scheme_response saucer_scheme_response;

typedef enum SAUCER_SCHEME_ERROR
{
    SAUCER_SCHEME_ERROR_NOT_FOUND = 404,
    SAUCER_SCHEME_ERROR_INVALID   = 400,
    SAUCER_SCHEME_ERROR_DENIED    = 401,
    SAUCER_SCHEME_ERROR_FAILED    = -1
} SAUCER_SCHEME_ERROR;

typedef void (*saucer_scheme_handler)(saucer_scheme_request *, saucer_scheme_executor *);

// .permission
typedef struct saucer_permission_request saucer_permission_request;

typedef enum SAUCER_PERMISSION_TYPE
{
    SAUCER_PERMISSION_TYPE_UNKNOWN       = 0,
    SAUCER_PERMISSION_TYPE_AUDIO_MEDIA   = 1 << 0,
    SAUCER_PERMISSION_TYPE_VIDEO_MEDIA   = 1 << 1,
    SAUCER_PERMISSION_TYPE_DESKTOP_MEDIA = 1 << 2,
    SAUCER_PERMISSION_TYPE_MOUSE_LOCK    = 1 << 3,
    SAUCER_PERMISSION_TYPE_DEVICE_INFO   = 1 << 4,
    SAUCER_PERMISSION_TYPE_LOCATION      = 1 << 5,
    SAUCER_PERMISSION_TYPE_CLIPBOARD     = 1 << 6,
    SAUCER_PERMISSION_TYPE_NOTIFICATION  = 1 << 7,
} SAUCER_PERMISSION_TYPE;

// .navigation
typedef struct saucer_navigation saucer_navigation;

// .webview
typedef struct saucer_webview saucer_webview;
typedef struct saucer_webview_options saucer_webview_options;

typedef enum SAUCER_STATE
{
    SAUCER_STATE_STARTED,
    SAUCER_STATE_FINISHED,
} SAUCER_STATE;

typedef enum SAUCER_STATUS
{
    SAUCER_STATE_HANDLED,
    SAUCER_STATE_UNHANDLED,
} SAUCER_STATUS;

typedef enum SAUCER_SCRIPT_TIME
{
    SAUCER_SCRIPT_TIME_CREATION,
    SAUCER_SCRIPT_TIME_READY,
} SAUCER_SCRIPT_TIME;

typedef enum SAUCER_WEBVIEW_EVENT
{
    SAUCER_WEBVIEW_EVENT_PERMISSION,
    SAUCER_WEBVIEW_EVENT_FULLSCREEN,
    SAUCER_WEBVIEW_EVENT_DOM_READY,
    SAUCER_WEBVIEW_EVENT_NAVIGATED,
    SAUCER_WEBVIEW_EVENT_NAVIGATE,
    SAUCER_WEBVIEW_EVENT_MESSAGE,
    SAUCER_WEBVIEW_EVENT_REQUEST,
    SAUCER_WEBVIEW_EVENT_FAVICON,
    SAUCER_WEBVIEW_EVENT_TITLE,
    SAUCER_WEBVIEW_EVENT_LOAD,
} SAUCER_WEBVIEW_EVENT;

typedef SAUCER_STATUS (*saucer_webview_event_permission)(saucer_webview *, saucer_permission_request *, void *);
typedef SAUCER_POLICY (*saucer_webview_event_fullscreen)(saucer_webview *, bool, void *);
typedef void (*saucer_webview_event_dom_ready)(saucer_webview *, void *);
typedef void (*saucer_webview_event_navigated)(saucer_webview *, saucer_url *, void *);
typedef SAUCER_POLICY (*saucer_webview_event_navigate)(saucer_webview *, saucer_navigation *, void *);
typedef SAUCER_STATUS (*saucer_webview_event_message)(saucer_webview *, const char *, size_t, void *);
typedef void (*saucer_webview_event_request)(saucer_webview *, saucer_url *, void *);
typedef void (*saucer_webview_event_favicon)(saucer_webview *, saucer_icon *, void *);
typedef void (*saucer_webview_event_title)(saucer_webview *, const char *, size_t, void *);
typedef void (*saucer_webview_event_load)(saucer_webview *, SAUCER_STATE, void *);

// .loop
typedef struct saucer_loop saucer_loop;

// .desktop
typedef struct saucer_desktop saucer_desktop;
typedef struct saucer_picker_options saucer_picker_options;


/// Saucer Functions

// .app
void saucer_screen_free(saucer_screen *);

const char *saucer_screen_name(saucer_screen *);
void saucer_screen_size(saucer_screen *, int *w, int *h);
void saucer_screen_position(saucer_screen *, int *x, int *y);

void saucer_application_options_free(saucer_application_options *);
saucer_application_options *saucer_application_options_new(const char *id);

void saucer_application_options_set_argc(saucer_application_options *, int);
void saucer_application_options_set_argv(saucer_application_options *, char **);
void saucer_application_options_set_quit_on_last_window_closed(saucer_application_options *, bool);

void saucer_application_free(saucer_application *);
saucer_application *saucer_application_new(saucer_application_options *, int *error);

bool saucer_application_thread_safe(saucer_application *);
void saucer_application_screens(saucer_application *, saucer_screen **, size_t *size);

void saucer_application_post(saucer_application *, saucer_post_callback, void *userdata);

void saucer_application_quit(saucer_application *);
int saucer_application_run(saucer_application *, saucer_run_callback, saucer_finish_callback, void *userdata);

size_t saucer_application_on(saucer_application *, SAUCER_APPLICATION_EVENT, void *callback, bool clearable,
                             void *userdata);

void saucer_application_once(saucer_application *, SAUCER_APPLICATION_EVENT, void *callback, void *userdata);

void saucer_application_off(saucer_application *, SAUCER_APPLICATION_EVENT, size_t);
void saucer_application_off_all(saucer_application *, SAUCER_APPLICATION_EVENT);

void saucer_application_native(saucer_application *, size_t idx, void *result, size_t *size);

const char *saucer_version();

// .stash
const uint8_t *saucer_stash_data(saucer_stash *);
size_t saucer_stash_size(saucer_stash *);

void saucer_stash_free(saucer_stash *);
saucer_stash *saucer_stash_copy(saucer_stash *);

saucer_stash *saucer_stash_new_from(uint8_t *, size_t);
saucer_stash *saucer_stash_new_view(const uint8_t *, size_t);
saucer_stash *saucer_stash_new_lazy(saucer_stash_lazy_callback, void *userdata);

saucer_stash *saucer_stash_new_from_str(const char *);
saucer_stash *saucer_stash_new_view_str(const char *);

saucer_stash *saucer_stash_new_empty();

// .icon
bool saucer_icon_empty(saucer_icon *);
saucer_stash *saucer_icon_data(saucer_icon *);

void saucer_icon_save(saucer_icon *, const char *);

void saucer_icon_free(saucer_icon *);
saucer_icon *saucer_icon_copy(saucer_icon *);

saucer_icon *saucer_icon_new_from_file(const char *, int *error);
saucer_icon *saucer_icon_new_from_stash(saucer_stash *, int *error);

void saucer_icon_native(saucer_icon *, size_t, void *, size_t *);

// .window
void saucer_window_free(saucer_window *);
saucer_window *saucer_window_new(saucer_application *, int *error);

bool saucer_window_visible(saucer_window *);
bool saucer_window_focused(saucer_window *);

bool saucer_window_minimized(saucer_window *);
bool saucer_window_maximized(saucer_window *);
bool saucer_window_resizable(saucer_window *);

bool saucer_window_fullscreen(saucer_window *);

bool saucer_window_always_on_top(saucer_window *);
bool saucer_window_click_through(saucer_window *);

void saucer_window_title(saucer_window *, char *, size_t *);

void saucer_window_background(saucer_window *, uint8_t *r, uint8_t *g, uint8_t *b, uint8_t *a);
int saucer_window_decorations(saucer_window *);

void saucer_window_size(saucer_window *, int *w, int *h);
void saucer_window_max_size(saucer_window *, int *w, int *h);
void saucer_window_min_size(saucer_window *, int *w, int *h);

void saucer_window_position(saucer_window *, int *x, int *y);
saucer_screen *saucer_window_screen(saucer_window *);

void saucer_window_hide(saucer_window *);
void saucer_window_show(saucer_window *);
void saucer_window_close(saucer_window *);

void saucer_window_focus(saucer_window *);

void saucer_window_start_drag(saucer_window *);
void saucer_window_start_resize(saucer_window *, SAUCER_WINDOW_EDGE);

void saucer_window_set_minimized(saucer_window *, bool);
void saucer_window_set_maximized(saucer_window *, bool);
void saucer_window_set_resizable(saucer_window *, bool);

void saucer_window_set_fullscreen(saucer_window *, bool);

void saucer_window_set_always_on_top(saucer_window *, bool);
void saucer_window_set_click_through(saucer_window *, bool);

void saucer_window_set_icon(saucer_window *, saucer_icon *);
void saucer_window_set_title(saucer_window *, const char *);

void saucer_window_set_background(saucer_window *, uint8_t r, uint8_t g, uint8_t b, uint8_t a);
void saucer_window_set_decorations(saucer_window *, SAUCER_WINDOW_DECORATION);

void saucer_window_set_size(saucer_window *, int w, int h);
void saucer_window_set_max_size(saucer_window *, int w, int h);
void saucer_window_set_min_size(saucer_window *, int w, int h);

void saucer_window_set_position(saucer_window *, int x, int y);

size_t saucer_window_on(saucer_window *, SAUCER_WINDOW_EVENT, void *callback, bool clearable, void *userdata);

void saucer_window_once(saucer_window *, SAUCER_WINDOW_EVENT, void *callback, void *userdata);

void saucer_window_off(saucer_window *, SAUCER_WINDOW_EVENT, size_t);
void saucer_window_off_all(saucer_window *, SAUCER_WINDOW_EVENT);

void saucer_window_native(saucer_window *, size_t, void *, size_t *);

// .url
void saucer_url_free(saucer_url *);
saucer_url *saucer_url_copy(saucer_url *);

saucer_url *saucer_url_new_parse(const char *, int *error);
saucer_url *saucer_url_new_from(const char *, int *error);
saucer_url *saucer_url_new_opts(const char *scheme, const char *host, size_t *port, const char *path);

void saucer_url_string(saucer_url *, char *, size_t *);

void saucer_url_path(saucer_url *, char *, size_t *);
void saucer_url_scheme(saucer_url *, char *, size_t *);

void saucer_url_host(saucer_url *, char *, size_t *);
bool saucer_url_port(saucer_url *, size_t *);

void saucer_url_user(saucer_url *, char *, size_t *);
void saucer_url_password(saucer_url *, char *, size_t *);

void saucer_url_native(saucer_url *, size_t, void *, size_t *);

// .scheme
void saucer_scheme_response_free(saucer_scheme_response *);
saucer_scheme_response *saucer_scheme_response_new(saucer_stash *, const char *mime);

void saucer_scheme_response_append_header(saucer_scheme_response *, const char *, const char *);
void saucer_scheme_response_set_status(saucer_scheme_response *, int);

void saucer_scheme_request_free(saucer_scheme_request *);
saucer_scheme_request *saucer_scheme_request_copy(saucer_scheme_request *);

saucer_url *saucer_scheme_request_url(saucer_scheme_request *);
void saucer_scheme_request_method(saucer_scheme_request *, char *, size_t *);

saucer_stash *saucer_scheme_request_content(saucer_scheme_request *);
void saucer_scheme_request_headers(saucer_scheme_request *, char *, size_t *);

void saucer_scheme_executor_free(saucer_scheme_executor *);
saucer_scheme_executor *saucer_scheme_executor_copy(saucer_scheme_executor *);

void saucer_scheme_executor_reject(saucer_scheme_executor *, SAUCER_SCHEME_ERROR);
void saucer_scheme_executor_accept(saucer_scheme_executor *, saucer_scheme_response *);

// .permission
void saucer_permission_request_free(saucer_permission_request *);
saucer_permission_request *saucer_permission_request_copy(saucer_permission_request *);

saucer_url *saucer_permission_request_url(saucer_permission_request *);
SAUCER_PERMISSION_TYPE saucer_permission_request_type(saucer_permission_request *);

void saucer_permission_request_accept(saucer_permission_request *, bool);

void saucer_permission_request_native(saucer_permission_request *, size_t, void *, size_t *);

// .navigation
saucer_url *saucer_navigation_url(saucer_navigation *);
bool saucer_navigation_new_window(saucer_navigation *);
bool saucer_navigation_redirection(saucer_navigation *);
bool saucer_navigation_user_initiated(saucer_navigation *);

// .webview
void saucer_webview_options_free(saucer_webview_options *);
saucer_webview_options *saucer_webview_options_new(saucer_window *);

void saucer_webview_options_set_attributes(saucer_webview_options *, bool);
void saucer_webview_options_set_persistent_cookies(saucer_webview_options *, bool);
void saucer_webview_options_set_hardware_acceleration(saucer_webview_options *, bool);

void saucer_webview_options_set_storage_path(saucer_webview_options *, const char *);
void saucer_webview_options_set_user_agent(saucer_webview_options *, const char *);
void saucer_webview_options_append_browser_flag(saucer_webview_options *, const char *);

void saucer_webview_free(saucer_webview *);
saucer_webview *saucer_webview_new(saucer_webview_options *, int *error);

saucer_url *saucer_webview_url(saucer_webview *, int *error);

saucer_icon *saucer_webview_favicon(saucer_webview *);
void saucer_webview_page_title(saucer_webview *, char *, size_t *);

bool saucer_webview_dev_tools(saucer_webview *);
bool saucer_webview_context_menu(saucer_webview *);

bool saucer_webview_force_dark(saucer_webview *);
void saucer_webview_background(saucer_webview *, uint8_t *r, uint8_t *g, uint8_t *b, uint8_t *a);

void saucer_webview_bounds(saucer_webview *, int *x, int *y, int *w, int *h);

void saucer_webview_set_url(saucer_webview *, saucer_url *);
void saucer_webview_set_url_str(saucer_webview *, const char *);

void saucer_webview_set_html(saucer_webview *, const char *);

void saucer_webview_set_dev_tools(saucer_webview *, bool);
void saucer_webview_set_context_menu(saucer_webview *, bool);

void saucer_webview_set_force_dark(saucer_webview *, bool);
void saucer_webview_set_background(saucer_webview *, uint8_t r, uint8_t g, uint8_t b, uint8_t a);

void saucer_webview_reset_bounds(saucer_webview *);
void saucer_webview_set_bounds(saucer_webview *, int x, int y, int w, int h);

void saucer_webview_back(saucer_webview *);
void saucer_webview_forward(saucer_webview *);

void saucer_webview_reload(saucer_webview *);

void saucer_webview_serve(saucer_webview *, const char *);
void saucer_webview_embed(saucer_webview *, const char *path, saucer_stash *content, const char *mime);

void saucer_webview_unembed_all(saucer_webview *);
void saucer_webview_unembed(saucer_webview *, const char *);

void saucer_webview_execute(saucer_webview *, const char *);
size_t saucer_webview_inject(saucer_webview *, const char *code, SAUCER_SCRIPT_TIME run_at, bool no_frames,
                             bool clearable);

void saucer_webview_uninject_all(saucer_webview *);
void saucer_webview_uninject(saucer_webview *, size_t);

void saucer_webview_handle_scheme(saucer_webview *, const char *, saucer_scheme_handler);
void saucer_webview_remove_scheme(saucer_webview *, const char *);

size_t saucer_webview_on(saucer_webview *, SAUCER_WEBVIEW_EVENT, void *callback, bool clearable, void *userdata);

void saucer_webview_once(saucer_webview *, SAUCER_WEBVIEW_EVENT, void *callback, void *userdata);

void saucer_webview_off(saucer_webview *, SAUCER_WEBVIEW_EVENT, size_t);
void saucer_webview_off_all(saucer_webview *, SAUCER_WEBVIEW_EVENT);

void saucer_webview_register_scheme(const char *);

void saucer_webview_native(saucer_webview *, size_t, void *, size_t *);

// .loop
void saucer_loop_free(saucer_loop *);
saucer_loop *saucer_loop_new(saucer_application *);

void saucer_loop_run(saucer_loop *);
void saucer_loop_iteration(saucer_loop *);

void saucer_loop_quit(saucer_loop *);

// .desktop
saucer_picker_options *saucer_picker_options_new();
void saucer_picker_options_free(saucer_picker_options *);
void saucer_picker_options_set_initial(saucer_picker_options *, const char *);
void saucer_picker_options_set_filters(saucer_picker_options *, const char *, size_t);

void saucer_desktop_free(saucer_desktop *);
saucer_desktop *saucer_desktop_new(saucer_application *);

void saucer_desktop_mouse_position(saucer_desktop *, int *x, int *y);

void saucer_picker_pick_file(saucer_desktop *, saucer_picker_options *, char *, size_t *, int *error);
void saucer_picker_pick_folder(saucer_desktop *, saucer_picker_options *, char *, size_t *, int *error);
void saucer_picker_pick_files(saucer_desktop *, saucer_picker_options *, char *, size_t *, int *error);
void saucer_picker_save(saucer_desktop *, saucer_picker_options *, char *, size_t *, int *error);

void saucer_desktop_open(saucer_desktop *, const char *);
