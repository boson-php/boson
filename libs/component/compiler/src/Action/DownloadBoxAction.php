<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @template-implements ActionInterface<DownloadBoxStatus>
 */
final readonly class DownloadBoxAction implements ActionInterface
{
    private HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }

    public function process(Configuration $config): iterable
    {
        yield DownloadBoxStatus::ReadyToDownload;

        if (\is_readable($config->boxPharPathname)) {
            return yield DownloadBoxStatus::Complete;
        }

        $localBoxStream = @\fopen($config->boxPharPathname, 'w+b');

        if ($localBoxStream === false) {
            throw new \RuntimeException(\sprintf(
                'Unable to create humbug/box phar package in "%s"',
                $config->boxPharPathname,
            ));
        }

        \flock($localBoxStream, \LOCK_EX);

        try {
            $externalBoxStream = $this->client->stream($this->client->request('GET', $config->boxUri));

            foreach ($externalBoxStream as $chunk) {
                \fwrite($localBoxStream, $chunk->getContent());

                yield DownloadBoxStatus::Downloading;
            }
        } catch (\Throwable $e) {
            \fclose($localBoxStream);
            \unlink($config->boxPharPathname);

            throw $e;
        }

        \flock($localBoxStream, \LOCK_UN);
        \fclose($localBoxStream);

        yield DownloadBoxStatus::Complete;
    }
}
