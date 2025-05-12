<?php

declare(strict_types=1);

namespace Boson\WebView\Internal;

/**
 * Packs the specified code into the given context.
 *
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView
 */
final readonly class WebViewContextPacker
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_ROOT_CONTEXT = 'window';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_DELIMITER = '.';

    public function __construct(
        private string $delimiter = self::DEFAULT_DELIMITER,
        private string $context = self::DEFAULT_ROOT_CONTEXT,
    ) {}

    /**
     * @param non-empty-list<non-empty-string> $context
     * @param non-empty-string $name
     * @param non-empty-string $code
     * @return non-empty-string
     */
    private function packFunction(array $context, string $name, string $code): string
    {
        return \vsprintf('%s["%s"] = %s', [
            \implode('.', $context),
            \addcslashes($name, '"'),
            $code,
        ]);
    }

    /**
     * @param non-empty-list<non-empty-string> $context
     * @param non-empty-string $name
     * @return non-empty-string
     */
    private function packElement(array $context, string $name): string
    {
        return \vsprintf('%s["%s"] = %1$s["%2$s"] || {}', [
            \implode('.', $context),
            \addcslashes($name, '"'),
        ]);
    }

    /**
     * @param non-empty-list<string> $segments
     * @param non-empty-string $code
     * @return list<non-empty-string>
     */
    private function packSegments(array $segments, string $code): array
    {
        $indexAt = \count($segments) - 1;
        $context = $this->context;

        $result = $packed = [];

        foreach ($segments as $index => $segment) {
            if ($segment === '') {
                continue;
            }

            $packed[] = $context;

            $result[] = $indexAt === $index
                ? $this->packFunction($packed, $segment, $code)
                : $this->packElement($packed, $segment);


            $context = $segment;
        }

        return $result;
    }

    /**
     * Returns packed javascript code.
     * ```
     * $packer->pack('foo', '42');
     * //
     * // window["foo"] = 42;
     * //
     *
     * $packer->pack('foo.some.any', '42);
     * //
     * // window["foo"] = window["foo"] || {};
     * // window.foo["some"] = window.foo["some"] || {};
     * // window.foo.some["any"] = 42;
     * //
     * ```
     *
     * @param non-empty-string $path
     * @return non-empty-string
     */
    public function pack(string $path, string $code): string
    {
        if (!\str_contains($path, $this->delimiter)) {
            return $this->packFunction($this->context, $path, $code);
        }

        $segments = $this->packSegments(\explode($this->delimiter, $path), $code);

        return \implode(';', \iterator_to_array($segments));
    }
}
