<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\PackApplication\BoxDownloadProcess;
use Boson\Component\Compiler\Workflow\PackApplication\BoxPackProcess;
use Boson\Component\Compiler\Workflow\PackApplication\PrepareProcess;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class PackApplicationWorkflow
{
    private PrepareProcess $prepare;
    private BoxDownloadProcess $download;
    private BoxPackProcess $pack;

    public function __construct()
    {
        $this->prepare = new PrepareProcess();
        $this->download = new BoxDownloadProcess();
        $this->pack = new BoxPackProcess();
    }

    /**
     * @return iterable<mixed, \UnitEnum>
     * @throws \JsonException
     * @throws TransportExceptionInterface
     * @throws \Throwable
     */
    public function process(Configuration $configuration): iterable
    {
        yield from $this->prepare->process($configuration);
        yield from $this->download->process($configuration);
        yield from $this->pack->process($configuration);
    }
}
