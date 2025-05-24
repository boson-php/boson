<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

use FFI\CData;
use FFI\CType;

/**
 * @mixin \FFI
 *
 * @phpstan-type SaucerOptionsType CData
 * @phpstan-type SaucerApplicationType CData
 * @phpstan-type SaucerStashType CData
 * @phpstan-type SaucerIconType CData
 * @phpstan-type SaucerNavigationType CData
 * @phpstan-type SaucerPreferencesType CData
 * @phpstan-type SaucerHandleType CData
 * @phpstan-type SaucerSchemeResponseType CData
 * @phpstan-type SaucerSchemeRequestType CData
 * @phpstan-type SaucerSchemeExecutorType CData
 * @phpstan-type SaucerScriptType CData
 * @phpstan-type SaucerEmbeddedFileType CData
 *
 * @phpstan-type SaucerPoolCallbackType callable():void
 * @phpstan-type SaucerPostCallbackType callable():void
 * @phpstan-type SaucerStashLazyCallbackType callable():SaucerStashType
 * @phpstan-type SaucerSchemeHandlerType callable(SaucerHandleType,SaucerSchemeRequestType,SaucerSchemeExecutorType):void
 * @phpstan-type SaucerOnMessageType callable(string):bool
 *
 * @phpstan-type UInt8ArrayType CData
 * @phpstan-type UnmanagedStringType CData
 *
 * @seal-properties
 * @seal-methods
 */
final readonly class LibSaucer
{
    public function __construct(
        ?string $library = null,
    ) {}

    /**
     * @param CType|non-empty-string $type
     */
    public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

    /**
     * @param CType|non-empty-string $type
     */
    public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

    /**
     * @return non-empty-string
     */
    public function boson_version(): string {}

    /**
     * @return SaucerOptionsType
     */
    public function saucer_options_new(string $id): CData {}

    /**
     * @param SaucerOptionsType $options
     */
    public function saucer_options_free(CData $options): void {}

    /**
     * @param SaucerOptionsType $options
     * @param int<-2147483648, 2147483647> $argc
     */
    public function saucer_options_set_argc(CData $options, int $argc): void {}

    /**
     * @param SaucerOptionsType $options
     */
    public function saucer_options_set_argv(CData $options, ?CData $argv): void {}

    /**
     * @param SaucerOptionsType $options
     */
    public function saucer_options_set_threads(CData $options, int $threads): void {}

    /**
     * @param SaucerOptionsType $options
     * @return SaucerApplicationType
     */
    public function saucer_application_init(CData $options): CData {}

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_free(CData $app): void {}

    /**
     * @return SaucerApplicationType
     */
    public function saucer_application_active(): CData {}

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_thread_safe(CData $app): bool {}

    /**
     * @param SaucerApplicationType $app
     * @param SaucerPoolCallbackType $callback
     */
    public function saucer_application_pool_submit(CData $app, callable $callback): void {}

    /**
     * @param SaucerApplicationType $app
     * @param SaucerPoolCallbackType $callback
     */
    public function saucer_application_pool_emplace(CData $app, callable $callback): void {}

    /**
     * @param SaucerApplicationType $app
     * @param SaucerPostCallbackType $callback
     */
    public function saucer_application_post(CData $app, callable $callback): void {}

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_quit(CData $app): void {}

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_run(CData $app): void {}

    /**
     * @param SaucerApplicationType $app
     */
    public function saucer_application_run_once(CData $app): void {}

    /**
     * @param SaucerStashType $stash
     */
    public function saucer_stash_free(CData $stash): void {}

    /**
     * @param SaucerStashType $stash
     */
    public function saucer_stash_size(CData $stash): int {}

    /**
     * @param SaucerStashType $stash
     * @return UInt8ArrayType
     */
    public function saucer_stash_data(CData $stash): CData {}

    /**
     * @param UInt8ArrayType $data
     * @return SaucerStashType
     */
    public function saucer_stash_from(CData $data, int $size): CData {}

    /**
     * @param UInt8ArrayType $data
     * @return SaucerStashType
     */
    public function saucer_stash_view(CData $data, int $size): CData {}

    /**
     * @param SaucerStashLazyCallbackType $callback
     * @return SaucerStashType
     */
    public function saucer_stash_lazy(callable $callback): CData {}

    /**
     * @param SaucerIconType $icon
     */
    public function saucer_icon_free(CData $icon): void {}

    /**
     * @param SaucerIconType $icon
     */
    public function saucer_icon_empty(CData $icon): bool {}

    /**
     * @param SaucerIconType $icon
     * @return SaucerStashType
     */
    public function saucer_icon_data(CData $icon): CData {}

    /**
     * @param SaucerIconType $icon
     */
    public function saucer_icon_save(CData $icon, string $path): void {}

    /**
     * @phpstan-type SaucerOutputIconType CData
     *
     * @param SaucerOutputIconType $resultIcon
     */
    public function saucer_icon_from_file(CData $resultIcon, string $file): void {}

    /**
     * @phpstan-type SaucerOutputIconType CData
     *
     * @param SaucerOutputIconType $resultIcon
     * @param SaucerStashType $stash
     */
    public function saucer_icon_from_data(CData $resultIcon, CData $stash): void {}

    public function saucer_memory_free(CData $data): void {}

    public function saucer_memory_alloc(int $size): CData {}

    /**
     * @param SaucerNavigationType $navigation
     */
    public function saucer_navigation_free(CData $navigation): void {}

    /**
     * @param SaucerNavigationType $navigation
     * @return UnmanagedStringType
     */
    public function saucer_navigation_url(CData $navigation): CData {}

    /**
     * @param SaucerNavigationType $navigation
     */
    public function saucer_navigation_new_window(CData $navigation): bool {}

    /**
     * @param SaucerNavigationType $navigation
     */
    public function saucer_navigation_redirection(CData $navigation): bool {}

    /**
     * @param SaucerNavigationType $navigation
     */
    public function saucer_navigation_user_initiated(CData $navigation): bool {}

    /**
     * @param SaucerApplicationType $app
     * @return SaucerPreferencesType
     */
    public function saucer_preferences_new(CData $app): CData {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_free(CData $preferences): void {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_set_persistent_cookies(CData $preferences, bool $enabled): void {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_set_hardware_acceleration(CData $preferences, bool $enabled): void {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_set_storage_path(CData $preferences, string $path): void {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_add_browser_flag(CData $preferences, string $flag): void {}

    /**
     * @param SaucerPreferencesType $preferences
     */
    public function saucer_preferences_set_user_agent(CData $preferences, string $userAgent): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_visible(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_focused(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_minimized(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_maximized(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_resizable(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_decorations(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_always_on_top(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_click_through(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     * @return UnmanagedStringType
     */
    public function saucer_window_title(CData $handle): CData {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_size(CData $handle, CData $width, CData $height): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_max_size(CData $handle, CData $width, CData $height): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_min_size(CData $handle, CData $width, CData $height): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_hide(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_show(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_close(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_focus(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_start_drag(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWindowEdge::SAUCER_WINDOW_EDGE_*|int-mask-of<SaucerWindowEdge::SAUCER_WINDOW_EDGE_*> $edge
     */
    public function saucer_window_start_resize(CData $handle, int $edge): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_minimized(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_maximized(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_resizable(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_decorations(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_always_on_top(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_click_through(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerIconType $icon
     */
    public function saucer_window_set_icon(CData $handle, CData $icon): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_window_set_title(CData $handle, string $title): void {}

    /**
     * @param SaucerHandleType $handle
     * @param int<-2147483648, 2147483647> $width
     * @param int<-2147483648, 2147483647> $height
     */
    public function saucer_window_set_size(CData $handle, int $width, int $height): void {}

    /**
     * @param SaucerHandleType $handle
     * @param int<-2147483648, 2147483647> $width
     * @param int<-2147483648, 2147483647> $height
     */
    public function saucer_window_set_max_size(CData $handle, int $width, int $height): void {}

    /**
     * @param SaucerHandleType $handle
     * @param int<-2147483648, 2147483647> $width
     * @param int<-2147483648, 2147483647> $height
     */
    public function saucer_window_set_min_size(CData $handle, int $width, int $height): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWindowEvent::SAUCER_WINDOW_EVENT_* $event
     */
    public function saucer_window_clear(CData $handle, int $event): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWindowEvent::SAUCER_WINDOW_EVENT_* $event
     */
    public function saucer_window_remove(CData $handle, int $event, int $id): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWindowEvent::SAUCER_WINDOW_EVENT_* $event
     */
    public function saucer_window_once(CData $handle, int $event, callable|CData $callback): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWindowEvent::SAUCER_WINDOW_EVENT_* $event
     */
    public function saucer_window_on(CData $handle, int $event, callable|CData $callback): int {}

    /**
     * @param SaucerStashType $data
     * @return SaucerSchemeResponseType
     */
    public function saucer_scheme_response_new(CData $data, string $mime): CData {}

    /**
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_response_free(CData $response): void {}

    /**
     * @param SaucerSchemeResponseType $response
     * @param int<-2147483648, 2147483647> $status
     */
    public function saucer_scheme_response_set_status(CData $response, int $status): void {}

    /**
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_response_add_header(CData $response, string $header, string $value): void {}

    /**
     * @param SaucerSchemeRequestType $request
     */
    public function saucer_scheme_request_free(CData $request): void {}

    /**
     * @param SaucerSchemeRequestType $request
     * @return UnmanagedStringType
     */
    public function saucer_scheme_request_url(CData $request): CData {}

    /**
     * @param SaucerSchemeRequestType $request
     * @return UnmanagedStringType
     */
    public function saucer_scheme_request_method(CData $request): CData {}

    /**
     * @param SaucerSchemeRequestType $request
     * @return SaucerStashType
     */
    public function saucer_scheme_request_content(CData $request): CData {}

    /**
     * @phpstan-type OutArrayOfStringType CData
     * @phpstan-type OutInteger CData
     *
     * @param SaucerSchemeRequestType $request
     * @param OutArrayOfStringType $headers
     * @param OutArrayOfStringType $values
     * @param OutInteger $count
     */
    public function saucer_scheme_request_headers(CData $request, CData $headers, CData $values, CData $count): void {}

    /**
     * @param SaucerSchemeExecutorType $executor
     */
    public function saucer_scheme_executor_free(CData $executor): void {}

    /**
     * @param SaucerSchemeExecutorType $executor
     * @param SaucerSchemeResponseType $response
     */
    public function saucer_scheme_executor_resolve(CData $executor, CData $response): void {}

    /**
     * @param SaucerSchemeExecutorType $executor
     * @param SaucerSchemeError::SAUCER_REQUEST_ERROR_* $error
     */
    public function saucer_scheme_executor_reject(CData $executor, int $error): void {}

    /**
     * @param SaucerLoadTime::SAUCER_LOAD_TIME_* $time
     * @return SaucerScriptType
     */
    public function saucer_script_new(string $code, int $time): CData {}

    /**
     * @param SaucerScriptType $script
     */
    public function saucer_script_free(CData $script): void {}

    /**
     * @param SaucerScriptType $script
     * @param SaucerWebFrame::SAUCER_WEB_FRAME_* $frame
     */
    public function saucer_script_set_frame(CData $script, int $frame): void {}

    /**
     * @param SaucerScriptType $script
     * @param SaucerLoadTime::SAUCER_LOAD_TIME_* $time
     */
    public function saucer_script_set_time(CData $script, int $time): void {}

    /**
     * @param SaucerScriptType $script
     */
    public function saucer_script_set_permanent(CData $script, bool $permanent): void {}

    /**
     * @param SaucerScriptType $script
     */
    public function saucer_script_set_code(CData $script, string $code): void {}

    /**
     * @param SaucerStashType $stash
     * @return SaucerEmbeddedFileType
     */
    public function saucer_embed(CData $stash, string $mime): CData {}

    /**
     * @param SaucerEmbeddedFileType $file
     */
    public function saucer_embed_free(CData $file): void {}

    /**
     * @param SaucerPreferencesType $preferences
     * @return SaucerHandleType
     */
    public function saucer_new(CData $preferences): CData {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_free(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerOnMessageType $callback
     */
    public function saucer_webview_on_message(CData $handle, callable $callback): void {}

    /**
     * @param SaucerHandleType $handle
     * @return SaucerIconType
     */
    public function saucer_webview_favicon(CData $handle): CData {}

    /**
     * @param SaucerHandleType $handle
     * @return UnmanagedStringType
     */
    public function saucer_webview_page_title(CData $handle): CData {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_dev_tools(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     * @return UnmanagedStringType
     */
    public function saucer_webview_url(CData $handle): CData {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_context_menu(CData $handle): bool {}

    /**
     * @phpstan-type OutUInt8Type CData
     *
     * @param SaucerHandleType $handle
     * @param OutUInt8Type $r
     * @param OutUInt8Type $g
     * @param OutUInt8Type $b
     * @param OutUInt8Type $a
     */
    public function saucer_webview_background(CData $handle, CData $r, CData $g, CData $b, CData $a): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_force_dark_mode(CData $handle): bool {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_set_dev_tools(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_set_context_menu(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_set_force_dark_mode(CData $handle, bool $enabled): void {}

    /**
     * @param SaucerHandleType $handle
     * @param int<0, 255> $r
     * @param int<0, 255> $g
     * @param int<0, 255> $b
     * @param int<0, 255> $a
     */
    public function saucer_webview_set_background(CData $handle, int $r, int $g, int $b, int $a): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_set_file(CData $handle, string $file): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_set_url(CData $handle, string $url): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_back(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_forward(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_reload(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerEmbeddedFileType $file
     * @param SaucerLaunch::SAUCER_LAUNCH_* $policy
     */
    public function saucer_webview_embed_file(CData $handle, string $name, CData $file, int $policy): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_serve(CData $handle, string $file): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_clear_scripts(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_clear_embedded(CData $handle): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_clear_embedded_file(CData $handle, string $file): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerScriptType $script
     */
    public function saucer_webview_inject(CData $handle, CData $script): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_execute(CData $handle, string $code): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerSchemeHandlerType $handler
     * @param SaucerLaunch::SAUCER_LAUNCH_* $policy
     */
    public function saucer_webview_handle_scheme(CData $handle, string $name, callable $handler, int $policy): void {}

    /**
     * @param SaucerHandleType $handle
     */
    public function saucer_webview_remove_scheme(CData $handle, string $name): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWebEvent::SAUCER_WEB_EVENT_* $event
     */
    public function saucer_webview_clear(CData $handle, int $event): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWebEvent::SAUCER_WEB_EVENT_* $event
     */
    public function saucer_webview_remove(CData $handle, int $event, int $id): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWebEvent::SAUCER_WEB_EVENT_* $event
     */
    public function saucer_webview_once(CData $handle, int $event, callable|CData $callback): void {}

    /**
     * @param SaucerHandleType $handle
     * @param SaucerWebEvent::SAUCER_WEB_EVENT_* $event
     */
    public function saucer_webview_on(CData $handle, int $event, callable|CData $callback): int {}

    public function saucer_register_scheme(string $name): void {}
}
