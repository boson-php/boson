<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

use Boson\Api\Dialog\Event\DirectorySelected;
use Boson\Api\Dialog\Event\DirectorySelecting;
use Boson\Api\Dialog\Event\FileSelected;
use Boson\Api\Dialog\Event\FileSelecting;
use Boson\Api\Dialog\Event\FilesSelecting;
use Boson\Api\Dialog\Event\UriOpened;
use Boson\Api\Dialog\Event\UriOpening;
use Boson\Api\LoadedApplicationExtension;
use Boson\Shared\Marker\RequiresDealloc;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\Dialog
 */
final class SaucerDialogApi extends LoadedApplicationExtension implements
    DialogApiInterface
{
    protected CData $ptr {
        /** @phpstan-ignore-next-line : PHPStan does not support property inheritance */
        get => $this->app->saucer->saucer_desktop_new(parent::$ptr::get());
    }

    private function applyDirectory(CData $options, ?string $directory): void
    {
        $directory ??= \getcwd();

        if (!\is_string($directory) || $directory === '') {
            return;
        }

        $this->app->saucer->saucer_picker_options_set_initial($options, $directory);
    }

    /**
     * @param list<non-empty-string> $filter
     */
    private function applyFilter(CData $options, array $filter): void
    {
        if ($filter === []) {
            return;
        }

        $this->app->saucer->saucer_picker_options_set_filters(
            $options,
            $filterAsString = \implode("\0", $filter),
            \strlen($filterAsString),
        );
    }

    /**
     * @param list<non-empty-string> $filter
     */
    #[RequiresDealloc]
    private function createOptions(?string $directory, array $filter): CData
    {
        $options = $this->app->saucer->saucer_picker_options_new();

        $this->applyDirectory($options, $directory);
        $this->applyFilter($options, $filter);

        return $options;
    }

    /**
     * @param list<non-empty-string> $filter
     * @param \Closure(CData, CData, CData|null, CData|null, CData|null): ?CData $selector
     *
     * @return non-empty-string|null
     */
    private function selectOne(?string $directory, array $filter, \Closure $selector): ?string
    {
        $options = $this->createOptions($directory, $filter);

        try {
            $length = $this->saucer->new('size_t');
            $selector($this->ptr, $options, null, \FFI::addr($length), null);

            if ($length->cdata === 0) {
                return null;
            }

            $result = $this->saucer->new("char[{$length->cdata}]");
            $selector($this->ptr, $options, \FFI::addr($result[0]), \FFI::addr($length), null);

            return \FFI::string(\FFI::addr($result[0]), $length->cdata);
        } finally {
            $this->app->saucer->saucer_picker_options_free($options);
        }
    }

    /**
     * @param list<non-empty-string> $filter
     * @param \Closure(CData, CData): ?CData $selector
     *
     * @return list<non-empty-string>
     */
    private function selectMany(?string $directory, array $filter, \Closure $selector): array
    {
        $options = $this->createOptions($directory, $filter);

        try {
            $length = $this->saucer->new('size_t');
            $selector($this->ptr, $options, null, \FFI::addr($length), null);

            if ($length->cdata === 0) {
                return [];
            }

            $result = $this->saucer->new("char[{$length->cdata}]");
            $selector($this->ptr, $options, \FFI::addr($result[0]), \FFI::addr($length), null);

            return \explode("\0", \FFI::string(\FFI::addr($result[0]), $length->cdata));
        } finally {
            $this->app->saucer->saucer_picker_options_free($options);
        }
    }

    public function open(string|\Stringable $uri): void
    {
        if (!$this->intent(new UriOpening($this->app, $uri))) {
            return;
        }

        if (($uri = (string) $uri) === '') {
            throw new \InvalidArgumentException('URI cannot be empty');
        }

        $this->app->saucer->saucer_desktop_open($this->ptr, $uri);

        $this->dispatch(new UriOpened($this->app, $uri));
    }

    public function selectDirectory(?string $directory = null, iterable $filter = []): ?string
    {
        if (!$this->intent(new DirectorySelecting($this->app, $directory, $filter))) {
            return null;
        }

        $filter = \iterator_to_array($filter, false);

        $result = $this->selectOne($directory, $filter, $this->app->saucer->saucer_picker_pick_folder(...));

        if ($result !== null) {
            $this->dispatch(new DirectorySelected($this->app, $result, $directory, $filter));
        }

        return $result;
    }

    public function selectFile(?string $directory = null, iterable $filter = []): ?string
    {
        if (!$this->intent(new FileSelecting($this->app, $directory, $filter))) {
            return null;
        }

        $filter = \iterator_to_array($filter, false);

        $result = $this->selectOne($directory, $filter, $this->app->saucer->saucer_picker_pick_file(...));

        if ($result !== null) {
            $this->dispatch(new FileSelected($this->app, $result, $directory, $filter));
        }

        return $result;
    }

    /**
     * @return list<non-empty-string>
     */
    public function selectFiles(?string $directory = null, iterable $filter = []): array
    {
        if (!$this->intent(new FilesSelecting($this->app, $directory, $filter))) {
            return [];
        }

        $filter = \iterator_to_array($filter, false);

        $result = $this->selectMany($directory, $filter, $this->app->saucer->saucer_picker_pick_files(...));

        foreach ($result as $selection) {
            $this->dispatch(new FileSelected($this->app, $selection, $directory, $filter));
        }

        return $result;
    }

    public function __destruct()
    {
        $this->app->saucer->saucer_desktop_free($this->ptr);
    }
}
