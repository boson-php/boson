<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Application;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\Window\Exception\WindowException;
use Boson\Window\WindowCreateInfo;
use Boson\Window\WindowId;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window\Manager
 */
final readonly class WindowHandlerFactory
{
    public function __construct(
        private SaucerInterface $saucer,
        private Application $app,
    ) {}

    public function create(WindowCreateInfo $info): WindowId
    {
        return WindowId::fromHandle(
            api: $this->saucer,
            handle: $this->createWindowHandle($info),
        );
    }

    #[RequiresDealloc]
    private function createWindowHandle(WindowCreateInfo $info): CData
    {
        $this->applyWindowOptionsBeforeCreated($info);

        $handle = $this->saucer->saucer_window_new($this->app->id->ptr, \FFI::addr(
            $error = $this->saucer->new('int'),
        ));

        if ($error->cdata !== 0) {
            throw new WindowException('An error occurred while creating window', $error->cdata);
        }

        $this->applyWindowOptionsAfterCreated($handle, $info);

        return $handle;
    }

    private function applyWindowOptionsBeforeCreated(WindowCreateInfo $info): void
    {
        // TODO
    }

    private function applyWindowOptionsAfterCreated(CData $window, WindowCreateInfo $info): void
    {
        $this->saucer->saucer_window_set_title($window, $info->title);
        $this->saucer->saucer_window_set_resizable($window, $info->resizable);
        $this->saucer->saucer_window_set_always_on_top($window, $info->alwaysOnTop);
        $this->saucer->saucer_window_set_click_through($window, $info->clickThrough);
        $this->saucer->saucer_window_set_size($window, $info->width, $info->height);
    }
}