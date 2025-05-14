<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\DataApiInterface;
use Boson\WebView\Api\ScriptsApiInterface;
use JetBrains\PhpStorm\Language;

final readonly class ElementInteractor
{
    private const string TEMPLATE = <<<'JS'
         (function () {
             try {
                var __%s = window.boson.components.instances["%1$s"];
                if (__%1$s) {
                    with (__%1$s) {
                        %s
                    }
                }
            } catch (e) {
                console.error(e);
            }
        })();
        JS;

    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $id,
        private DataApiInterface $data,
        private ScriptsApiInterface $scripts,
    ) {}

    /**
     * @api
     */
    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->scripts->eval(\sprintf(self::TEMPLATE, $this->id, $code));
    }

    /**
     * @api
     */
    public function get(#[Language('JavaScript')] string $code): mixed
    {
        return $this->data->get(\sprintf(self::TEMPLATE, $this->id, 'return ' . $code));
    }

    /**
     * @api
     */
    public function getStatements(#[Language('JavaScript')] string $code): mixed
    {
        return $this->data->get(\sprintf(self::TEMPLATE, $this->id, $code));
    }
}
