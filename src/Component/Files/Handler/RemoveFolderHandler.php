<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Handler;

use PersonalGalaxy\Files\{
    Command\RemoveFolder,
    Handler\RemoveFolderHandler as Handler,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class RemoveFolderHandler
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

    public function __invoke(RemoveFolder $wished): void
    {
        $this->dbal->execute(
            (new Query)
                ->match(null, ['Files', 'Folder'])
                ->withProperty('identity', '{folder}')
                ->withParameter('folder', (string) $wished->identity())
                ->linkedTo(null, ['Files'])
                ->through('CHILD_OF', 'rels', Relationship::LEFT)
                ->withAnyDistance()
                ->with('collect(rels) as rels')
                ->foreach('(rel in rels | DELETE rel)')
        );
        ($this->handle)($wished);
    }
}
