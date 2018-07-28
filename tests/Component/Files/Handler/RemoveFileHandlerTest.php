<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Handler;

use PersonalGalaxy\X\Component\Files\Handler\RemoveFileHandler;
use PersonalGalaxy\Files\{
    Handler\RemoveFileHandler as Handler,
    Command\RemoveFile,
    Repository\FileRepository,
    Entity\File,
    Entity\File\Identity,
    Entity\File\Name,
    Entity\Folder\Identity as Folder,
};
use Innmind\Filesystem\{
    Adapter,
    MediaType,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class RemoveFileHandlerTest extends TestCase
{
    public function testInvokation()
    {
        $handle = new RemoveFileHandler(
            new Handler(
                $repository = $this->createMock(FileRepository::class),
                $this->createMock(Adapter::class)
            ),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (:Files:Folder)<-[rel:CHILD_OF]-(:Files:File { identity: {file} }) DELETE rel' &&
                    $query->parameters()->get('file')->value() === 'file uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('file uuid');
        $repository
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($file = File::add(
                $identity,
                new Name('foo'),
                $this->createMock(Folder::class),
                $this->createMock(MediaType::class)
            ));
        $file->trash();

        $this->assertNull($handle(new RemoveFile(
            $identity
        )));
    }
}
