<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Handler;

use PersonalGalaxy\Files\{
    Command\RemoveFile,
    Handler\RemoveFileHandler as Handler,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class RemoveFileHandler
{
    private $handle;
    private $dbal;

    public function __construct(
        Handler $handle,
        Connection $dbal
    ) {
        $this->handle = $handle;
        $this->dbal = $dbal;
    }

    public function __invoke(RemoveFile $wished): void
    {
        $this->dbal->execute(
            (new Query)
                ->match(null, ['Files', 'Folder'])
                ->linkedTo(null, ['Files', 'File'])
                ->withProperty('identity', '{file}')
                ->withParameter('file', (string) $wished->identity())
                ->through('CHILD_OF', 'rel', Relationship::LEFT)
                ->delete('rel')
        );
        ($this->handle)($wished);
    }
}
