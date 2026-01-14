<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Component\Http\Response;
use Boson\Contracts\Http\Component\StatusCodeInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\WebView\WebView;

final readonly class ProductionErrorHandler implements ErrorHandlerInterface
{
    private const string ERROR_TEMPLATE = <<<'HTML'
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
            <meta http-equiv="X-UA-Compatible" content="ie=edge" />
            <title>An Internal Error Occurred</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Roboto+Condensed:wght@100..900&display=swap" rel="stylesheet">
            <style>
            :root {
                --color-bg: #0d1119;
                --color-bg-button: #3A1309;
                --color-bg-button-hover: #601A08;
                --color-text: rgba(255, 255, 255, 0.7);
                --color-text-brand: #F93904;
                --color-text-button: #F93904;

                --font-fallback-main: BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
                --font-fallback-mono: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace;

                --font-title: 'Roboto Condensed', var(--font-fallback-main);
                --font-mono: 'JetBrains Mono', var(--font-fallback-mono);
                --font-main: Inter, var(--font-fallback-main);

                --font-size: 17px;
                --font-line-height: 1.7;

                --font-size-h1: 44px;
                --font-size-h2: 24px;
            }

            html,
            body {
                background: var(--color-bg);
                color: var(--color-text);
                font-family: var(--font-main), sans-serif;
                font-size: var(--font-size);
                line-height: var(--font-line-height);
                margin: 0;
                padding: 0;
                transition: opacity 1s ease;
            }

            h1, h2, h3, h4, h5, h6 {
                font-family: var(--font-title), sans-serif;
                color: var(--color-text);
                margin: 0;
                padding: 0;
            }

            h1 {
                font-size: var(--font-size-h1);
                font-weight: 600;
                font-style: normal;
                color: var(--color-text-brand);
                display: block;
            }

            h2 {
                font-size: var(--font-size-h2);
                font-weight: 300;
                font-style: normal;
                display: block;
            }

            button {
                font-family: var(--font-mono), sans-serif;
                cursor: pointer;
                border: none;
                border-radius: 3px;
                letter-spacing: 1px;
                color: inherit;
                transition-duration: 0.1s;
                background: var(--color-bg-button);
                text-transform: uppercase;
                padding: 0 2em;
                display: inline-block;
                line-height: 56px;
                margin-top: 50px;
                height: 56px;
                gap: 1em;
                justify-content: inherit;
                align-items: center;
                white-space: nowrap;
            }

            button:hover {
                color: var(--color-text-button);
                background: var(--color-bg-button-hover);
            }

            .layout {
                padding: 25px;
                box-sizing: border-box;
                width: 100vw;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            }
            </style>
        </head>
        <body style="opacity: 0">
            <main class="layout">
                <h1>An Internal Error Occurred</h1>
                <h2>To solve the problem, please contact the developer</h2>
                <p>
                    <button onclick="document.location.reload()">reload</button>
                </p>
            </main>
            <script>
                setTimeout(function () {
                    document.body.style.removeProperty('opacity');
                }, 100);
            </script>
        </body>
        </html>
        HTML;

    public function __construct(
        private StatusCodeInterface $status = self::DEFAULT_ERROR_CODE,
    ) {}

    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): ResponseInterface
    {
        return new Response(self::ERROR_TEMPLATE, $this->status);
    }
}
