<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\Files\Event\FolderWasAdded;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindFolderToParent
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(FolderWasAdded $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('parent', ['Files', 'Folder'])
                ->withProperty('identity', '{parent}')
                ->match('folder', ['Files', 'Folder'])
                ->withProperty('identity', '{folder}')
                ->with('parent', 'folder')
                ->create('folder')
                ->linkedTo('parent')
                ->through('CHILD_OF', null, Relationship::RIGHT)
                ->withParameters([
                    'parent' => (string) $event->parent(),
                    'folder' => (string) $event->identity(),
                ])
        );
    }
}
