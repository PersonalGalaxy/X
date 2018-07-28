<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\Files\Event\FolderWasMovedToADifferentParent;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindFolderToNewParent
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(FolderWasMovedToADifferentParent $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('folder', ['Files', 'Folder'])
                ->withProperty('identity', '{folder}')
                ->linkedTo(null, ['Files', 'Folder'])
                ->through('CHILD_OF', 'rel')
                ->delete('rel')
                ->match('parent', ['Files', 'Folder'])
                ->withProperty('identity', '{parent}')
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
