<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\X\Component\Files\Listener\BindFileToNewFolder;
use PersonalGalaxy\Files\{
    Entity\File\Identity,
    Entity\Folder\Identity as Folder,
    Event\FileWasMovedToADifferentFolder,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindFileToNewFolderTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindFileToNewFolder(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (file:Files:File { identity: {file} })-[rel:CHILD_OF]-(:Files:Folder) DELETE rel MATCH (folder:Files:Folder { identity: {folder} }) WITH file, folder CREATE (file)-[:CHILD_OF]->(folder)' &&
                    $query->parameters()->get('file')->value() === 'file uuid' &&
                    $query->parameters()->get('folder')->value() === 'folder uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('file uuid');
        $folder = $this->createMock(Folder::class);
        $folder
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('folder uuid');

        $this->assertNull($bind(new FileWasMovedToADifferentFolder(
            $identity,
            $folder
        )));
    }
}
