<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\Files\Event\FileWasAdded;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindFileToFolder
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(FileWasAdded $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('file', ['Files', 'File'])
                ->withProperty('identity', '{file}')
                ->match('folder', ['Files', 'Folder'])
                ->withProperty('identity', '{folder}')
                ->with('file', 'folder')
                ->create('file')
                ->linkedTo('folder')
                ->through('CHILD_OF', null, Relationship::RIGHT)
                ->withParameters([
                    'file' => (string) $event->identity(),
                    'folder' => (string) $event->folder(),
                ])
        );
    }
}
