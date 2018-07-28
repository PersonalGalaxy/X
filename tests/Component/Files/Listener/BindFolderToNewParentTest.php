<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\X\Component\Files\Listener\BindFolderToNewParent;
use PersonalGalaxy\Files\{
    Entity\Folder\Identity,
    Event\FolderWasMovedToADifferentParent,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindFolderToNewParentTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindFolderToNewParent(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (folder:Files:Folder { identity: {folder} })-[rel:CHILD_OF]-(:Files:Folder) DELETE rel MATCH (parent:Files:Folder { identity: {parent} }) WITH parent, folder CREATE (folder)-[:CHILD_OF]->(parent)' &&
                    $query->parameters()->get('parent')->value() === 'parent uuid' &&
                    $query->parameters()->get('folder')->value() === 'folder uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('folder uuid');
        $parent = $this->createMock(Identity::class);
        $parent
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('parent uuid');

        $this->assertNull($bind(new FolderWasMovedToADifferentParent(
            $identity,
            $parent
        )));
    }
}
