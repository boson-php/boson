<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

use Boson\Component\Compiler\Configuration;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class BoxDownloadProcess
{
    private HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }

    /**
     * @return iterable<mixed, BoxDownloadProcessStatus>
     * @throws TransportExceptionInterface
     * @throws \Throwable
     */
    public function process(Configuration $configuration): iterable
    {
        yield BoxDownloadProcessStatus::ReadyToDownload;

        if (\is_readable($configuration->boxPharPathname)) {
            return yield BoxDownloadProcessStatus::Complete;
        }

        $localBoxStream = \fopen($configuration->boxPharPathname, 'w+b');
        \flock($localBoxStream, \LOCK_EX);

        try {
            $externalBoxStream = $this->client->stream($this->client->request('GET', $configuration->boxUri));

            foreach ($externalBoxStream as $chunk) {
                \fwrite($localBoxStream, $chunk->getContent());

                yield BoxDownloadProcessStatus::Downloading;
            }
        } catch (\Throwable $e) {
            \fclose($localBoxStream);
            \unlink($configuration->boxPharPathname);

            throw $e;
        }

        \flock($localBoxStream, \LOCK_UN);
        \fclose($localBoxStream);

        yield BoxDownloadProcessStatus::Complete;
    }
}
