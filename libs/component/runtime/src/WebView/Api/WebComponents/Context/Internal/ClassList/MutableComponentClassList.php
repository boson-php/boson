<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\ClassList;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use Boson\WebView\Api\WebComponents\Context\MutableClassListInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
final readonly class MutableComponentClassList extends ComponentClassList implements
    MutableClassListInterface
{
    public function __construct(
        private ScriptEvaluatorInterface $scripts,
        SyncDataRetrieverInterface $data,
    ) {
        parent::__construct($data);
    }

    public function add(string $class, string ...$other): void
    {
        $arguments = [$this->classToArgument($class)];

        foreach ($other as $argument) {
            $arguments[] = $this->classToArgument($argument);
        }

        $this->scripts->eval(\sprintf(
            'this.classList.add(%s)',
            \implode(',', $arguments),
        ));
    }

    public function remove(string $class, string ...$other): void
    {
        $arguments = [$this->classToArgument($class)];

        foreach ($other as $argument) {
            $arguments[] = $this->classToArgument($argument);
        }

        $this->scripts->eval(\sprintf(
            'this.classList.remove(%s)',
            \implode(',', $arguments),
        ));
    }

    public function replace(string $fromClass, string $toClass): bool
    {
        return (bool) $this->data->get(\sprintf(
            'this.classList.replace(%s, %s)',
            $this->classToArgument($fromClass),
            $this->classToArgument($toClass),
        ));
    }

    public function toggle(string $class): bool
    {
        return (bool) $this->data->get(\sprintf(
            'this.classList.toggle(%s)',
            $this->classToArgument($class),
        ));
    }
}
