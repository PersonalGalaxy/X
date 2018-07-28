<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Handler;

use PersonalGalaxy\X\Component\Files\Handler\EmptyTrashHandler;
use PersonalGalaxy\Files\{
    Handler\EmptyTrashHandler as Handler,
    Handler\RemoveFileHandler,
    Handler\RemoveFolderHandler,
    Repository\FileRepository,
    Repository\FolderRepository,
};
use Innmind\Filesystem\Adapter;
use Innmind\Neo4j\DBAL\Connection;
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class EmptyTrashHandlerTest extends TestCase
{
    public function testInvokation()
    {
        $handle = new EmptyTrashHandler(
            new Handler(
                $files = $this->createMock(FileRepository::class),
                $folders = $this->createMock(FolderRepository::class),
                $removeFile = new RemoveFileHandler(
                    $files,
                    $this->createMock(Adapter::class)
                ),
                new RemoveFolderHandler(
                    $files,
                    $folders,
                    $removeFile
                )
            ),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (:Files { trashed: {trashed} })<-[rels:CHILD_OF*]-(:Files) WITH collect(rels) as rels FOREACH (rel in rels | DELETE rel)' &&
                    $query->parameters()->get('trashed')->value() === true;
            }));

        $this->assertNull($handle());
    }
}
