<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

use FFI\CData;
use FFI\CType;

/**
 * @internal this is an INTERNAL CLASS for PHPStan and IDE autocomplete only,
 *           please do not use it in your code
 *
 * @mixin \FFI
 *
 * @phpstan-type SaucerScreenType CData
 * @phpstan-type SaucerScreenTypePtr CData
 * @phpstan-type SaucerApplicationType CData
 * @phpstan-type SaucerApplicationOptionsType CData
 * @phpstan-type SaucerStashType CData
 * @phpstan-type SaucerIconType CData
 * @phpstan-type SaucerWindowType CData
 * @phpstan-type SaucerUrlType CData
 * @phpstan-type SaucerPermissionRequestType CData
 * @phpstan-type SaucerNavigationType CData
 * @phpstan-type SaucerWebviewType CData
 * @phpstan-type SaucerWebviewOptionsType CData
 * @phpstan-type SaucerSchemeExecutorType CData
 * @phpstan-type SaucerSchemeRequestType CData
 * @phpstan-type SaucerSchemeResponseType CData
 *
 * @phpstan-type SaucerLoopType CData
 *
 * @phpstan-type SaucerDesktopType CData
 * @phpstan-type SaucerPickerOptionsType CData
 *
 * @phpstan-type SizeTPtr CData
 * @phpstan-type UInt8Ptr CData
 * @phpstan-type IntPtr CData
 * @phpstan-type CharPtr CData
 *
 * @phsptan-type saucer_application_event_quit callable(SaucerApplicationType, CData|null): (Policy::SAUCER_POLICY_*)
 * @phsptan-type saucer_post_callback callable(CData|null): (void)
 * @phsptan-type saucer_run_callback callable(SaucerApplicationType, CData|null): (void)
 * @phsptan-type saucer_finish_callback callable(SaucerApplicationType, CData|null): (void)
 * @phsptan-type saucer_stash_lazy_callback callable(CData|null): (SaucerStashType)
 * @phsptan-type saucer_window_event_decorated callable(SaucerWindowType, WindowDecoration::SAUCER_WINDOW_DECORATION_*, CData|null): (void)
 * @phsptan-type saucer_window_event_maximize callable(SaucerWindowType, bool, CData|null): (void)
 * @phsptan-type saucer_window_event_minimize callable(SaucerWindowType, bool, CData|null): (void)
 * @phsptan-type saucer_window_event_closed callable(SaucerWindowType, CData|null): (void)
 * @phsptan-type saucer_window_event_resize callable(SaucerWindowType, int, int, CData|null): (void)
 * @phsptan-type saucer_window_event_focus callable(SaucerWindowType, bool, CData|null): (void)
 * @phsptan-type saucer_window_event_close callable(SaucerWindowType, CData|null): (Policy::SAUCER_POLICY_*)
 * @phsptan-type saucer_scheme_handler callable(SaucerSchemeRequestType, SaucerSchemeExecutorType): (void)
 * @phsptan-type saucer_webview_event_permission callable(SaucerWebviewType, SaucerPermissionRequestType, CData|null): (Status::SAUCER_STATE_*)
 * @phsptan-type saucer_webview_event_fullscreen callable(SaucerWebviewType, bool, CData|null): (Policy::SAUCER_POLICY_*)
 * @phsptan-type saucer_webview_event_dom_ready callable(SaucerWebviewType, CData|null): (void)
 * @phsptan-type saucer_webview_event_navigated callable(SaucerWebviewType, SaucerUrlType, CData|null): (void)
 * @phsptan-type saucer_webview_event_navigate callable(SaucerWebviewType, SaucerNavigationType, CData|null): (Policy::SAUCER_POLICY_*)
 * @phsptan-type saucer_webview_event_message callable(SaucerWebviewType, string|null, int<0, max>, CData|null): (Status::SAUCER_STATE_*)
 * @phsptan-type saucer_webview_event_request callable(SaucerWebviewType, SaucerUrlType, CData|null): (void)
 * @phsptan-type saucer_webview_event_favicon callable(SaucerWebviewType, SaucerIconType, CData|null): (void)
 * @phsptan-type saucer_webview_event_title callable(SaucerWebviewType, string|null, int<0, max>, CData|null): (void)
 * @phsptan-type saucer_webview_event_load callable(SaucerWebviewType, State::SAUCER_STATE_*, CData|null): (void)
 *
 * @seal-properties
 * @seal-methods
 */
interface SaucerInterface
{
    /**
     * @param CType|non-empty-string $type
     */
    public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData;

    /**
     * @param CType|non-empty-string $type
     */
    public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData;

    /**
     * @return non-empty-string
     */
    public function boson_version(): string;

    /**
     * @param SaucerScreenType $screen
     */
    public function saucer_screen_free(CData $screen): void;

    /**
     * @param SaucerScreenType $screen
     */
    public function saucer_screen_name(CData $screen): string;

    /**
     * @param SaucerScreenType $screen
     * @param IntPtr $w
     * @param IntPtr $h
     */
    public function saucer_screen_size(CData $screen, CData $w, CData $h): void;

    /**
     * @param SaucerScreenType $screen
     * @param IntPtr $x
     * @param IntPtr $y
     */
    public function saucer_screen_position(CData $screen, CData $x, CData $y): void;

    /**
     * @param SaucerApplicationOptionsType $options
     */
    public function saucer_application_options_free(CData $options): void;

    /**
     * @param non-empty-string $id
     * @return SaucerApplicationOptionsType
     */
    public function saucer_application_options_new(string $id): CData;

    /**
     * @param SaucerApplicationOptionsType $options
     * @param int<-2147483648, 2147483647> $argc
     */
    public function saucer_application_options_set_argc(CData $options, int $argc): void;

    /**
     * @param SaucerApplicationOptionsType $options
     */
    public function saucer_application_options_set_argv(CData $options, CData|null $argv): void;

    /**
     * @param SaucerApplicationOptionsType $options
     */
    public function saucer_application_options_set_quit_on_last_window_closed(CData $options, bool $enable): void;

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_free(CData $app): void;

    /**
     * @param SaucerApplicationOptionsType $options
     * @param IntPtr $error
     * @return SaucerApplicationType
     */
    public function saucer_application_new(CData $options, CData $error): CData;

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_thread_safe(CData $app): bool;

    /**
     * @param SaucerApplicationType $app
     * @param SaucerScreenTypePtr $screens
     * @param SizeTPtr $size
     */
    public function saucer_application_screens(CData $app, CData|null $screens, CData|null $size): void;

    /**
     * @param SaucerApplicationType $app
     * @param (callable(CData|null): void)|CData $onPost
     */
    public function saucer_application_post(CData $app, CData|callable $onPost, CData|null $data): void;

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_quit(CData $app): void;

    /**
     * @param SaucerApplicationType $app
     * @param (callable(SaucerApplicationType, CData|null): void)|CData $onRun
     * @param (callable(SaucerApplicationType, CData|null): void)|CData $onFinish
     * @return int<-2147483648, 2147483647>
     */
    public function saucer_application_run(CData $app, CData|callable $onRun, CData|callable $onFinish, CData|null $data): int;

    /**
     * @param SaucerApplicationType $app
     * @param (ApplicationEvent::SAUCER_APPLICATION_EVENT_*) $event
     * @return int<0, max>
     */
    public function saucer_application_on(CData $app, int $event, CData|callable $callback, bool $clearable, CData|null $data): int;

    /**
     * @param SaucerApplicationType $app
     * @param (ApplicationEvent::SAUCER_APPLICATION_EVENT_*) $event
     */
    public function saucer_application_once(CData $app, int $event, CData|callable $callback, CData|null $data): void;

    /**
     * @param SaucerApplicationType $app
     * @param (ApplicationEvent::SAUCER_APPLICATION_EVENT_*) $event
     * @param int<0, max> $id
     */
    public function saucer_application_off(CData $app, int $event, int $id): void;

    /**
     * @param SaucerApplicationType $app
     * @param (ApplicationEvent::SAUCER_APPLICATION_EVENT_*) $event
     */
    public function saucer_application_off_all(CData $app, int $event): void;

    /**
     * @param SaucerApplicationType $app
     * @param int<0, max> $idx
     * @param SizeTPtr $size
     */
    public function saucer_application_native(CData $app, int $idx, CData|null $result, CData|null $size): void;

    public function saucer_version(): string;

    /**
     * @param SaucerStashType $stash
     * @return UInt8Ptr
     */
    public function saucer_stash_data(CData $stash): CData;

    /**
     * @param SaucerStashType $stash
     * @return int<0, max>
     */
    public function saucer_stash_size(CData $stash): int;

    /**
     * @param SaucerStashType $stash
     */
    public function saucer_stash_free(CData $stash): void;

    /**
     * @param SaucerStashType $stash
     * @return SaucerStashType
     */
    public function saucer_stash_copy(CData $stash): CData;

    /**
     * @param UInt8Ptr $data
     * @param int<0, max> $size
     * @return SaucerStashType
     */
    public function saucer_stash_new_from(CData $data, int $size): CData;

    /**
     * @param UInt8Ptr $data
     * @param int<0, max> $size
     * @return SaucerStashType
     */
    public function saucer_stash_new_view(CData $data, int $size): CData;

    /**
     * @param (callable(CData|null):SaucerStashType)|CData $callback
     * @return SaucerStashType
     */
    public function saucer_stash_new_lazy(CData|callable $callback, CData|null $data): CData;

    public function saucer_stash_new_from_str(string $data): CData;

    public function saucer_stash_new_view_str(string $data): CData;

    public function saucer_stash_new_empty(): CData;

    /**
     * @param SaucerIconType $icon
     */
    public function saucer_icon_empty(CData $icon): bool;

    /**
     * @param SaucerIconType $icon
     * @return SaucerStashType
     */
    public function saucer_icon_data(CData $icon): CData;

    /**
     * @param SaucerIconType $icon
     * @param non-empty-string $path
     */
    public function saucer_icon_save(CData $icon, string $path): void;

    /**
     * @param SaucerIconType $icon
     */
    public function saucer_icon_free(CData $icon): void;

    /**
     * @param SaucerIconType $icon
     * @return SaucerIconType
     */
    public function saucer_icon_copy(CData $icon): CData;

    /**
     * @param non-empty-string $path
     * @param IntPtr $error
     * @return SaucerIconType
     */
    public function saucer_icon_new_from_file(string $path, CData $error): CData;

    /**
     * @param SaucerStashType $stash
     * @param IntPtr $error
     * @return SaucerIconType
     */
    public function saucer_icon_new_from_stash(CData $stash, CData $error): CData;

    /**
     * @param SaucerIconType $icon
     * @param int<0, max> $idx
     * @param CData|null $result
     * @param CData|null $size
     */
    public function saucer_icon_native(CData $icon, int $idx, CData|null $result, CData|null $size): void;


    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_free(CData $window): void;

    /**
     * @param SaucerApplicationType $app
     * @param IntPtr $error
     * @return SaucerWindowType
     */
    public function saucer_window_new(CData $app, CData $error): CData;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_visible(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_focused(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_minimized(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_maximized(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_resizable(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_fullscreen(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_always_on_top(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_click_through(CData $window): bool;

    /**
     * @param SaucerWindowType $window
     * @param CharPtr|null $title
     * @param SizeTPtr|null $size
     */
    public function saucer_window_title(CData $window, CData|null $title, CData|null $size): void;

    /**
     * @param SaucerWindowType $window
     * @param UInt8Ptr $r
     * @param UInt8Ptr $g
     * @param UInt8Ptr $b
     * @param UInt8Ptr $a
     */
    public function saucer_window_background(CData $window, CData $r, CData $g, CData $b, CData $a): void;

    /**
     * @param SaucerWindowType $window
     * @return int<-2147483648, 2147483647>
     */
    public function saucer_window_decorations(CData $window): int;

    /**
     * @param SaucerWindowType $window
     * @param IntPtr $w
     * @param IntPtr $h
     */
    public function saucer_window_size(CData $window, CData $w, CData $h): void;

    /**
     * @param SaucerWindowType $window
     * @param IntPtr $w
     * @param IntPtr $h
     */
    public function saucer_window_max_size(CData $window, CData $w, CData $h): void;

    /**
     * @param SaucerWindowType $window
     * @param IntPtr $w
     * @param IntPtr $h
     */
    public function saucer_window_min_size(CData $window, CData $w, CData $h): void;

    /**
     * @param SaucerWindowType $window
     * @param IntPtr $x
     * @param IntPtr $y
     */
    public function saucer_window_position(CData $window, CData $x, CData $y): void;

    /**
     * @param SaucerWindowType $window
     * @return SaucerScreenType
     */
    public function saucer_window_screen(CData $window): CData;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_hide(CData $window): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_show(CData $window): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_close(CData $window): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_focus(CData $window): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_start_drag(CData $window): void;

    /**
     * @param SaucerWindowType $window
     * @param (WindowEdge::SAUCER_WINDOW_EDGE_*) $edge
     */
    public function saucer_window_start_resize(CData $window, int $edge): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_minimized(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_maximized(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_resizable(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_fullscreen(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_always_on_top(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_click_through(CData $window, bool $state): void;

    /**
     * @param SaucerWindowType $window
     * @param SaucerIconType $icon
     */
    public function saucer_window_set_icon(CData $window, CData $icon): void;

    /**
     * @param SaucerWindowType $window
     */
    public function saucer_window_set_title(CData $window, string $title): void;

    /**
     * @param SaucerWindowType $window
     * @param int<0, 255> $r
     * @param int<0, 255> $g
     * @param int<0, 255> $b
     * @param int<0, 255> $a
     */
    public function saucer_window_set_background(CData $window, int $r, int $g, int $b, int $a): void;

    /**
     * @param SaucerWindowType $window
     * @param (WindowDecoration::SAUCER_WINDOW_DECORATION_*) $decorations
     */
    public function saucer_window_set_decorations(CData $window, int $decorations): void;

    /**
     * @param SaucerWindowType $window
     * @param int<-2147483648, 2147483647> $w
     * @param int<-2147483648, 2147483647> $h
     */
    public function saucer_window_set_size(CData $window, int $w, int $h): void;

    /**
     * @param SaucerWindowType $window
     * @param int<-2147483648, 2147483647> $w
     * @param int<-2147483648, 2147483647> $h
     */
    public function saucer_window_set_max_size(CData $window, int $w, int $h): void;

    /**
     * @param SaucerWindowType $window
     * @param int<-2147483648, 2147483647> $w
     * @param int<-2147483648, 2147483647> $h
     */
    public function saucer_window_set_min_size(CData $window, int $w, int $h): void;

    /**
     * @param SaucerWindowType $window
     * @param int<-2147483648, 2147483647> $x
     * @param int<-2147483648, 2147483647> $y
     */
    public function saucer_window_set_position(CData $window, int $x, int $y): void;

    /**
     * @param SaucerWindowType $window
     * @param (WindowEvent::SAUCER_WINDOW_EVENT_*) $event
     * @return int<0, max>
     */
    public function saucer_window_on(CData $window, int $event, CData|callable $callback, bool $clearable, CData|null $data): int;

    /**
     * @param SaucerWindowType $window
     * @param (WindowEvent::SAUCER_WINDOW_EVENT_*) $event
     */
    public function saucer_window_once(CData $window, int $event, CData|callable $callback, CData|null $data): void;

    /**
     * @param SaucerWindowType $window
     * @param (WindowEvent::SAUCER_WINDOW_EVENT_*) $event
     * @param int<0, max> $id
     */
    public function saucer_window_off(CData $window, int $event, int $id): void;

    /**
     * @param SaucerWindowType $window
     * @param (WindowEvent::SAUCER_WINDOW_EVENT_*) $event
     */
    public function saucer_window_off_all(CData $window, int $event): void;

    /**
     * @param SaucerWindowType $window
     * @param int<0, max> $idx
     * @param SizeTPtr|null $size
     */
    public function saucer_window_native(CData $window, int $idx, CData|null $result, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     */
    public function saucer_url_free(CData $url): void;

    /**
     * @param SaucerUrlType $url
     * @return SaucerUrlType
     */
    public function saucer_url_copy(CData $url): CData;

    /**
     * @param IntPtr $error
     * @return SaucerUrlType
     */
    public function saucer_url_new_parse(string $data, CData $error): CData;

    /**
     * @param IntPtr $error
     * @return SaucerUrlType
     */
    public function saucer_url_new_from(string $path, CData $error): CData;

    /**
     * @param SizeTPtr $port
     * @return SaucerUrlType
     */
    public function saucer_url_new_opts(string $scheme, string $host, CData $port, string $path): CData;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $string
     * @param SizeTPtr|null $size
     */
    public function saucer_url_string(CData $url, CData|null $string, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $path
     * @param SizeTPtr|null $size
     */
    public function saucer_url_path(CData $url, CData|null $path, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $scheme
     * @param SizeTPtr|null $size
     */
    public function saucer_url_scheme(CData $url, CData|null $scheme, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $host
     * @param SizeTPtr|null $size
     */
    public function saucer_url_host(CData $url, CData|null $host, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr $port
     */
    public function saucer_url_port(CData $url, CData $port): bool;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $user
     * @param SizeTPtr|null $size
     */
    public function saucer_url_user(CData $url, CData|null $user, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param CharPtr|null $password
     * @param SizeTPtr|null $size
     */
    public function saucer_url_password(CData $url, CData|null $password, CData|null $size): void;

    /**
     * @param SaucerUrlType $url
     * @param int<0, max> $idx
     * @param SizeTPtr|null $size
     */
    public function saucer_url_native(CData $url, int $idx, CData|null $result, CData|null $size): void;

    /**
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_response_free(CData $response): void;

    /**
     * @param SaucerStashType $stash
     * @return SaucerSchemeResponseType
     */
    public function saucer_scheme_response_new(CData $stash, string $mime): CData;

    /**
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_response_append_header(CData $response, string $name, string $value): void;

    /**
     * @param SaucerSchemeResponseType $response
     * @param int<-2147483648, 2147483647> $status
     */
    public function saucer_scheme_response_set_status(CData $response, int $status): void;

    /**
     * @param SaucerSchemeRequestType $request
     */
    public function saucer_scheme_request_free(CData $request): void;

    /**
     * @param SaucerSchemeRequestType $request
     * @return SaucerSchemeRequestType
     */
    public function saucer_scheme_request_copy(CData $request): CData;

    /**
     * @param SaucerSchemeRequestType $request
     * @return SaucerUrlType
     */
    public function saucer_scheme_request_url(CData $request): CData;

    /**
     * @param SaucerSchemeRequestType $request
     * @param CharPtr|null $method
     * @param SizeTPtr|null $size
     */
    public function saucer_scheme_request_method(CData $request, CData|null $method, CData|null $size): void;

    /**
     * @param SaucerSchemeRequestType $request
     * @return SaucerStashType
     */
    public function saucer_scheme_request_content(CData $request): CData;

    /**
     * @param SaucerSchemeRequestType $request
     * @param CharPtr|null $headers
     * @param SizeTPtr|null $size
     */
    public function saucer_scheme_request_headers(CData $request, CData|null $headers, CData|null $size): void;

    /**
     * @param SaucerSchemeExecutorType $executor
     */
    public function saucer_scheme_executor_free(CData $executor): void;

    /**
     * @param SaucerSchemeExecutorType $executor
     * @return SaucerSchemeExecutorType
     */
    public function saucer_scheme_executor_copy(CData $executor): CData;

    /**
     * @param SaucerSchemeExecutorType $executor
     * @param (SchemeError::SAUCER_SCHEME_ERROR_*) $code
     */
    public function saucer_scheme_executor_reject(CData $executor, int $code): void;

    /**
     * @param SaucerSchemeExecutorType $executor
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_executor_accept(CData $executor, CData $response): void;

    /**
     * @param SaucerPermissionRequestType $request
     */
    public function saucer_permission_request_free(CData $request): void;

    /**
     * @param SaucerPermissionRequestType $request
     * @return SaucerPermissionRequestType
     */
    public function saucer_permission_request_copy(CData $request): CData;

    /**
     * @param SaucerPermissionRequestType $request
     * @return SaucerUrlType
     */
    public function saucer_permission_request_url(CData $request): CData;

    /**
     * @param SaucerPermissionRequestType $request
     * @return (PermissionType::SAUCER_PERMISSION_TYPE_*)
     */
    public function saucer_permission_request_type(CData $request): int;

    /**
     * @param SaucerPermissionRequestType $request
     */
    public function saucer_permission_request_accept(CData $request, bool $state): void;

    /**
     * @param SaucerPermissionRequestType $request
     * @param int<0, max> $idx
     * @param SizeTPtr|null $size
     */
    public function saucer_permission_request_native(CData $request, int $idx, CData|null $result, CData|null $size): void;

    /**
     * @param SaucerNavigationType $nav
     * @return SaucerUrlType
     */
    public function saucer_navigation_url(CData $nav): CData;

    /**
     * @param SaucerNavigationType $nav
     */
    public function saucer_navigation_new_window(CData $nav): bool;

    /**
     * @param SaucerNavigationType $nav
     */
    public function saucer_navigation_redirection(CData $nav): bool;

    /**
     * @param SaucerNavigationType $nav
     */
    public function saucer_navigation_user_initiated(CData $nav): bool;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_free(CData $options): void;

    /**
     * @param SaucerWindowType $window
     * @return SaucerWebviewOptionsType
     */
    public function saucer_webview_options_new(CData $window): CData;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_set_attributes(CData $options, bool $enabled): void;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_set_persistent_cookies(CData $options, bool $enabled): void;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_set_hardware_acceleration(CData $options, bool $enabled): void;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_set_storage_path(CData $options, string $value): void;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_set_user_agent(CData $options, string $value): void;

    /**
     * @param SaucerWebviewOptionsType $options
     */
    public function saucer_webview_options_append_browser_flag(CData $options, string $value): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_free(CData $webview): void;

    /**
     * @param SaucerWebviewOptionsType $options
     * @param IntPtr $error
     * @return SaucerWebviewType
     */
    public function saucer_webview_new(CData $options, CData $error): CData;

    /**
     * @param SaucerWebviewType $webview
     * @param IntPtr $error
     * @return SaucerUrlType
     */
    public function saucer_webview_url(CData $webview, CData $error): CData;

    /**
     * @param SaucerWebviewType $webview
     * @return SaucerIconType
     */
    public function saucer_webview_favicon(CData $webview): CData;

    /**
     * @param SaucerWebviewType $webview
     * @param CharPtr|null $title
     * @param SizeTPtr|null $size
     */
    public function saucer_webview_page_title(CData $webview, CData|null $title, CData|null $size): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_dev_tools(CData $webview): bool;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_context_menu(CData $webview): bool;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_force_dark(CData $webview): bool;

    /**
     * @param SaucerWebviewType $webview
     * @param UInt8Ptr $r
     * @param UInt8Ptr $g
     * @param UInt8Ptr $b
     * @param UInt8Ptr $a
     */
    public function saucer_webview_background(CData $webview, CData $r, CData $g, CData $b, CData $a): void;

    /**
     * @param SaucerWebviewType $webview
     * @param IntPtr $x
     * @param IntPtr $y
     * @param IntPtr $w
     * @param IntPtr $h
     */
    public function saucer_webview_bounds(CData $webview, CData $x, CData $y, CData $w, CData $h): void;

    /**
     * @param SaucerWebviewType $webview
     * @param SaucerUrlType $url
     */
    public function saucer_webview_set_url(CData $webview, CData $url): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_set_url_str(CData $webview, string $url): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_set_html(CData $webview, string $html): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_set_dev_tools(CData $webview, bool $enabled): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_set_context_menu(CData $webview, bool $enabled): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_set_force_dark(CData $webview, bool $enabled): void;

    /**
     * @param SaucerWebviewType $webview
     * @param int<0, 255> $r
     * @param int<0, 255> $g
     * @param int<0, 255> $b
     * @param int<0, 255> $a
     */
    public function saucer_webview_set_background(CData $webview, int $r, int $g, int $b, int $a): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_reset_bounds(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     * @param int<-2147483648, 2147483647> $x
     * @param int<-2147483648, 2147483647> $y
     * @param int<-2147483648, 2147483647> $w
     * @param int<-2147483648, 2147483647> $h
     */
    public function saucer_webview_set_bounds(CData $webview, int $x, int $y, int $w, int $h): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_back(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_forward(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_reload(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_serve(CData $webview, string $path): void;

    /**
     * @param SaucerWebviewType $webview
     * @param SaucerStashType $content
     */
    public function saucer_webview_embed(CData $webview, string $path, CData $content, string $mime): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_unembed_all(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_unembed(CData $webview, string $path): void;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_execute(CData $webview, string $code): void;

    /**
     * @param SaucerWebviewType $webview
     * @param (ScriptTime::SAUCER_SCRIPT_TIME_*) $type
     * @return int<0, max>
     */
    public function saucer_webview_inject(CData $webview, string $code, int $type, bool $noFrames, bool $clearable): int;

    /**
     * @param SaucerWebviewType $webview
     */
    public function saucer_webview_uninject_all(CData $webview): void;

    /**
     * @param SaucerWebviewType $webview
     * @param int<0, max> $id
     */
    public function saucer_webview_uninject(CData $webview, int $id): void;

    /**
     * @param SaucerWebviewType $webview
     * @param non-empty-string $scheme
     * @param (callable(SaucerSchemeRequestType, SaucerSchemeExecutorType): void)|CData $handler
     */
    public function saucer_webview_handle_scheme(CData $webview, string $scheme, callable|CData $handler): void;

    /**
     * @param SaucerWebviewType $webview
     * @param non-empty-string $scheme
     */
    public function saucer_webview_remove_scheme(CData $webview, string $scheme): void;

    /**
     * @param SaucerWebviewType $webview
     * @param (WebViewEvent::SAUCER_WEBVIEW_EVENT_*) $event
     * @return int<0, max>
     */
    public function saucer_webview_on(CData $webview, int $event, CData|callable $callback, bool $clearable, CData|null $data): int;

    /**
     * @param SaucerWebviewType $webview
     * @param (WebViewEvent::SAUCER_WEBVIEW_EVENT_*) $event
     */
    public function saucer_webview_once(CData $webview, int $event, CData|callable $callback, CData|null $data): void;

    /**
     * @param SaucerWebviewType $webview
     * @param (WebViewEvent::SAUCER_WEBVIEW_EVENT_*) $event
     * @param int<0, max> $id
     */
    public function saucer_webview_off(CData $webview, int $event, int $id): void;

    /**
     * @param SaucerWebviewType $webview
     * @param (WebViewEvent::SAUCER_WEBVIEW_EVENT_*) $event
     */
    public function saucer_webview_off_all(CData $webview, int $event): void;

    /**
     * @param non-empty-string $scheme
     */
    public function saucer_webview_register_scheme(string $scheme): void;

    /**
     * @param SaucerWebviewType $webview
     * @param int<0, max> $idx
     * @param SizeTPtr|null $size
     */
    public function saucer_webview_native(CData $webview, int $idx, CData|null $result, CData|null $size): void;

    /**
     * @param SaucerLoopType $loop
     */
    public function saucer_loop_free(CData $loop): void;

    /**
     * @param SaucerApplicationType $app
     * @return SaucerLoopType
     */
    public function saucer_loop_new(CData $app): CData;

    /**
     * @param SaucerLoopType $loop
     */
    public function saucer_loop_run(CData $loop): void;

    /**
     * @param SaucerLoopType $loop
     */
    public function saucer_loop_iteration(CData $loop): void;

    /**
     * @param SaucerLoopType $loop
     */
    public function saucer_loop_quit(CData $loop): void;

    /**
     * @return SaucerPickerOptionsType
     */
    public function saucer_picker_options_new(): CData;

    /**
     * @param SaucerPickerOptionsType $options
     */
    public function saucer_picker_options_free(CData $options): void;

    /**
     * @param SaucerPickerOptionsType $options
     */
    public function saucer_picker_options_set_initial(CData $options, string $initial): void;

    /**
     * @param SaucerPickerOptionsType $options
     * @param int<0, max> $size
     */
    public function saucer_picker_options_set_filters(CData $options, string $filters, int $size): void;

    /**
     * @param SaucerDesktopType $desktop
     */
    public function saucer_desktop_free(CData $desktop): void;

    /**
     * @param SaucerApplicationType $app
     * @return SaucerDesktopType
     */
    public function saucer_desktop_new(CData $app): CData;

    /**
     * @param SaucerDesktopType $desktop
     * @param int<-2147483648, 2147483647> $x
     * @param int<-2147483648, 2147483647> $y
     */
    public function saucer_desktop_mouse_position(CData $desktop, int $x, int $y): void;

    /**
     * @param SaucerDesktopType $desktop
     * @param SaucerPickerOptionsType $options
     * @param CharPtr|null $file
     * @param SizeTPtr|null $size
     * @param IntPtr|null $error
     */
    public function saucer_picker_pick_file(CData $desktop, CData $options, CData|null $file, CData|null $size, CData|null $error): void;

    /**
     * @param SaucerDesktopType $desktop
     * @param SaucerPickerOptionsType $options
     * @param CharPtr|null $folder
     * @param SizeTPtr|null $size
     * @param IntPtr|null $error
     */
    public function saucer_picker_pick_folder(CData $desktop, CData $options, CData|null $folder, CData|null $size, CData|null $error): void;

    /**
     * @param SaucerDesktopType $desktop
     * @param SaucerPickerOptionsType $options
     * @param CharPtr|null $files
     * @param SizeTPtr|null $size
     * @param IntPtr|null $error
     */
    public function saucer_picker_pick_files(CData $desktop, CData $options, CData|null $files, CData|null $size, CData|null $error): void;

    /**
     * @param SaucerDesktopType $desktop
     * @param SaucerPickerOptionsType $options
     * @param CharPtr|null $location
     * @param SizeTPtr|null $size
     * @param IntPtr|null $error
     */
    public function saucer_picker_save(CData $desktop, CData $options, CData|null $location, CData|null $size, CData|null $error): void;

    /**
     * @param SaucerDesktopType $desktop
     */
    public function saucer_desktop_open(CData $desktop, string $path): void;
}
