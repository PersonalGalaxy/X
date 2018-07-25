<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\X\Component\Files\Listener\BindFolderToParent;
use PersonalGalaxy\Files\{
    Entity\Folder\Identity,
    Entity\Folder\Name,
    Event\FolderWasAdded,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindFolderToParentTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindFolderToParent(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (parent:Files:Folder { identity: {parent} }), (folder:Files:Folder { identity: {folder} }) WITH parent, folder CREATE (folder)-[:CHILD_OF]->(parent)' &&
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

        $this->assertNull($bind(new FolderWasAdded(
            $identity,
            new Name('foo'),
            $parent
        )));
    }
}
