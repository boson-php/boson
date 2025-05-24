<?php

namespace PHPSTORM_META {

    registerArgumentsSet('boson_http_header_name',
        'accept-ch',
        'accept-encoding',
        'accept-language',
        'accept-patch',
        'accept-post',
        'accept-ranges',
        'access-control-allow-credentials',
        'access-control-allow-headers',
        'access-control-allow-methods',
        'access-control-allow-origin',
        'access-control-expose-headers',
        'access-control-max-age',
        'access-control-request-headers',
        'access-control-request-method',
        'age',
        'allow',
        'alt-svc',
        'alt-used',
        'attribution-reporting-eligibleexperimental',
        'attribution-reporting-register-sourceexperimental',
        'attribution-reporting-register-triggerexperimental',
        'authorization',
        'available-dictionaryexperimental',
        'cache-control',
        'clear-site-data',
        'connection',
        'content-digest',
        'content-disposition',
        'content-dpr',
        'content-encoding',
        'content-language',
        'content-length',
        'content-location',
        'content-range',
        'content-security-policy',
        'content-security-policy-report-only',
        'content-type',
        'cookie',
        'critical-chexperimental',
        'cross-origin-embedder-policy',
        'cross-origin-opener-policy',
        'cross-origin-resource-policy',
        'date',
        'device-memory',
        'dictionary-idexperimental',
        'dnt',
        'downlinkexperimental',
        'dpr',
        'early-dataexperimental',
        'ectexperimental',
        'etag',
        'expect',
        'expect-ct',
        'expires',
        'forwarded',
        'from',
        'host',
        'if-match',
        'if-modified-since',
        'if-none-match',
        'if-range',
        'if-unmodified-since',
        'keep-alive',
        'last-modified',
        'link',
        'location',
        'max-forwards',
        'nelexperimental',
        'no-vary-searchexperimental',
        'observe-browsing-topicsexperimental',
        'origin',
        'origin-agent-cluster',
        'permissions-policyexperimental',
        'pragma',
        'prefer',
        'preference-applied',
        'priority',
        'proxy-authenticate',
        'proxy-authorization',
        'range',
        'referer',
        'referrer-policy',
        'refresh',
        'report-to',
        'reporting-endpoints',
        'repr-digest',
        'retry-after',
        'rttexperimental',
        'save-dataexperimental',
        'sec-browsing-topicsexperimental',
        'sec-ch-prefers-color-schemeexperimental',
        'sec-ch-prefers-reduced-motionexperimental',
        'sec-ch-prefers-reduced-transparencyexperimental',
        'sec-ch-uaexperimental',
        'sec-ch-ua-archexperimental',
        'sec-ch-ua-bitnessexperimental',
        'sec-ch-ua-form-factorsexperimental',
        'sec-ch-ua-full-version',
        'sec-ch-ua-full-version-listexperimental',
        'sec-ch-ua-mobileexperimental',
        'sec-ch-ua-modelexperimental',
        'sec-ch-ua-platformexperimental',
        'sec-ch-ua-platform-versionexperimental',
        'sec-ch-ua-wow64experimental',
        'sec-fetch-dest',
        'sec-fetch-mode',
        'sec-fetch-site',
        'sec-fetch-user',
        'sec-gpcexperimental',
        'sec-purpose',
        'sec-speculation-tagsexperimental',
        'sec-websocket-accept',
        'sec-websocket-extensions',
        'sec-websocket-key',
        'sec-websocket-protocol',
        'sec-websocket-version',
        'server',
        'server-timing',
        'service-worker',
        'service-worker-allowed',
        'service-worker-navigation-preload',
        'set-cookie',
        'set-login',
        'sourcemap',
        'speculation-rulesexperimental',
        'strict-transport-security',
        'supports-loading-modeexperimental',
        'te',
        'timing-allow-origin',
        'tk',
        'trailer',
        'transfer-encoding',
        'upgrade',
        'upgrade-insecure-requests',
        'use-as-dictionaryexperimental',
        'user-agent',
        'vary',
        'via',
        'viewport-width',
        'want-content-digest',
        'want-repr-digest',
        'warning',
        'width',
        'www-authenticate',
        'x-content-type-options',
        'x-dns-prefetch-control',
        'x-forwarded-for',
        'x-forwarded-host',
        'x-forwarded-proto',
        'x-frame-options',
        'x-permitted-cross-domain-policies',
        'x-powered-by',
        'x-robots-tag',
        'x-xss-protection'
    );

    registerArgumentsSet('boson_http_method',
        'GET',
        'HEAD',
        'OPTIONS',
        'TRACE',
        'PUT',
        'DELETE',
        'POST',
        'PATCH',
        'CONNECT'
    );

    registerArgumentsSet('boson_http_status_code',
        100, 101, 102, 103,
        110, 111, 112, 113, 199,
        200, 201, 202, 203, 204, 205, 206, 207, 208,
        214, 226, 299,
        300, 301, 302, 303, 304, 305, 306, 307, 308,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409,
        410, 411, 412, 413, 414, 415, 416, 417, 418,
        421, 422, 423, 424, 425, 426, 428, 429,
        431, 444, 451, 499,
        500, 501, 502, 503, 504, 505, 506, 507, 508,
        510, 511, 599
    );

    registerArgumentsSet('boson_http_json_encoding_flags', JSON_HEX_QUOT
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_NUMERIC_CHECK
        | JSON_PRETTY_PRINT
        | JSON_UNESCAPED_SLASHES
        | JSON_FORCE_OBJECT
        | JSON_PRESERVE_ZERO_FRACTION
        | JSON_UNESCAPED_UNICODE
        | JSON_PARTIAL_OUTPUT_ON_ERROR
        | JSON_UNESCAPED_LINE_TERMINATORS
        | JSON_THROW_ON_ERROR
    );

    expectedArguments(\Boson\Contracts\Http\HeadersInterface::first(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Contracts\Http\HeadersInterface::all(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Contracts\Http\HeadersInterface::has(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Contracts\Http\HeadersInterface::contains(), 0, argumentsSet('boson_http_header_name'));

    expectedArguments(\Boson\Http\HeadersMap::first(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Http\HeadersMap::all(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Http\HeadersMap::has(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Http\HeadersMap::contains(), 0, argumentsSet('boson_http_header_name'));

    expectedArguments(\Boson\Contracts\Http\MutableHeadersInterface::set(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Contracts\Http\MutableHeadersInterface::add(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Contracts\Http\MutableHeadersInterface::remove(), 0, argumentsSet('boson_http_header_name'));

    expectedArguments(\Boson\Http\MutableHeadersMap::set(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Http\MutableHeadersMap::add(), 0, argumentsSet('boson_http_header_name'));
    expectedArguments(\Boson\Http\MutableHeadersMap::remove(), 0, argumentsSet('boson_http_header_name'));

    expectedArguments(\Boson\Http\Request::__construct(), 0, argumentsSet('boson_http_method'));
    expectedArguments(\Boson\Http\MutableRequest::__construct(), 0, argumentsSet('boson_http_method'));

    expectedArguments(\Boson\Http\Response::__construct(), 2, argumentsSet('boson_http_status_code'));
    expectedReturnValues(\Boson\Http\Response::$status, argumentsSet('boson_http_status_code'));
    expectedArguments(\Boson\Http\JsonResponse::__construct(), 2, argumentsSet('boson_http_status_code'));
    expectedReturnValues(\Boson\Http\JsonResponse::$status, argumentsSet('boson_http_status_code'));
    expectedArguments(\Boson\Http\JsonResponse::__construct(), 3, argumentsSet('boson_http_json_encoding_flags'));

    exitPoint(\Boson\Application::run());
}
