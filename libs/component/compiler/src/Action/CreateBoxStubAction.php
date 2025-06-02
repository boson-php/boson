<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;

/**
 * @template-implements ActionInterface<CreateBoxStubStatus>
 */
final readonly class CreateBoxStubAction implements ActionInterface
{
    public function process(Configuration $config): iterable
    {
        yield CreateBoxStubStatus::ReadyToCreate;

        $stub = \str_replace(
            search: ['{name}', '{entrypoint}'],
            replace: [$config->name, $config->entrypoint],
            subject: \file_get_contents(__DIR__ . '/../../resources/stub.php'),
        );

        \file_put_contents($config->boxStubPathname, $stub);

        yield CreateBoxStubStatus::Created;
    }
}
