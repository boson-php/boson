<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow;

use Boson\Component\Compiler\Action\CreateBoxConfigAction;
use Boson\Component\Compiler\Action\CreateBuildDirectoryAction;
use Boson\Component\Compiler\Action\DownloadBoxAction;
use Boson\Component\Compiler\Action\PackBoxAction;
use Boson\Component\Compiler\Configuration;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class PackApplicationWorkflow
{
    /**
     * @return iterable<mixed, \UnitEnum>
     * @throws \JsonException
     * @throws TransportExceptionInterface
     * @throws \Throwable
     */
    public function process(Configuration $config): iterable
    {
        // Create build directory
        yield from new CreateBuildDirectoryAction()
            ->process($config);

        // Create box json config
        yield from new CreateBoxConfigAction()
            ->process($config);

        // Download box package
        yield from new DownloadBoxAction()
            ->process($config);

        // Pack box package
        yield from new PackBoxAction()
            ->process($config);
    }
}
