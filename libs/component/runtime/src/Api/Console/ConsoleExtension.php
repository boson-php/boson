<?php

declare(strict_types=1);

namespace Boson\Api\Console;

use Boson\Api\Console\Driver\VoidConsole;
use Boson\Api\Console\Driver\WindowsConsole;
use Boson\Api\Console\Event\ConsoleDetached;
use Boson\Api\Console\Event\ConsoleDetaching;
use Boson\Api\OperatingSystem\OperatingSystemExtension;
use Boson\Api\OperatingSystem\OperatingSystemInfoInterface;
use Boson\Application;
use Boson\Component\OsInfo\Family;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\Extension;
use FFI\Env\Runtime;

/**
 * @template-extends Extension<Application>
 */
#[DependsOn(OperatingSystemExtension::class)]
final class ConsoleExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): ConsoleApiInterface
    {
        $console = $this->createDriver($ctx->get(OperatingSystemInfoInterface::class));

        if ($this->shouldDetachConsole($ctx)) {
            $listener->dispatch($intention = new ConsoleDetaching($ctx, $console));

            if ($intention->isCancelled) {
                return $console;
            }

            $console->detach();

            $listener->dispatch(new ConsoleDetached($ctx, $console));
        }

        return $console;
    }

    private function shouldDetachConsole(Application $app): bool
    {
        return !$app->isDebug
            && $this->isRunningInPhar();
    }

    private function createDriver(OperatingSystemInfoInterface $operatingSystem): ConsoleApiInterface
    {
        if (!Runtime::isAvailable()) {
            return new VoidConsole();
        }

        if ($operatingSystem->family->is(Family::Windows)) {
            return new WindowsConsole();
        }

        return new VoidConsole();
    }

    private function isRunningInPhar(): bool
    {
        return \class_exists(\Phar::class)
            && \Phar::running() !== '';
    }
}
