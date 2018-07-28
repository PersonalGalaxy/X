<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Handler;

use PersonalGalaxy\X\Component\Files\Handler\RemoveFolderHandler;
use PersonalGalaxy\Files\{
    Handler\RemoveFolderHandler as Handler,
    Handler\RemoveFileHandler,
    Command\RemoveFolder,
    Repository\FileRepository,
    Repository\FolderRepository,
    Entity\File,
    Entity\Folder,
    Entity\Folder\Identity,
    Entity\Folder\Name,
};
use Innmind\Filesystem\Adapter;
use Innmind\Neo4j\DBAL\Connection;
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class RemoveFolderHandlerTest extends TestCase
{
    public function testInvokation()
    {
        $handle = new RemoveFolderHandler(
            new Handler(
                $files = $this->createMock(FileRepository::class),
                $folders = $this->createMock(FolderRepository::class),
                new RemoveFileHandler(
                    $files,
                    $this->createMock(Adapter::class)
                )
            ),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (:Files:Folder { identity: {folder} })<-[rels:CHILD_OF*]-(:Files) WITH collect(rels) as rels FOREACH (rel in rels | DELETE rel)' &&
                    $query->parameters()->get('folder')->value() === 'folder uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->any())
            ->method('__toString')
            ->willReturn('folder uuid');
        $folders
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($folder = Folder::add(
                $identity,
                new Name('foo'),
                $this->createMock(Identity::class)
            ));
        $folder->trash();

        $this->assertNull($handle(new RemoveFolder(
            $identity
        )));
    }
}
