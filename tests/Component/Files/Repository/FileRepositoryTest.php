<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Repository;

use PersonalGalaxy\X\Component\Files\{
    Repository\FileRepository,
    Entity\File as Id,
};
use PersonalGalaxy\Files\{
    Repository\FileRepository as FileRepositoryInterface,
    Entity\File,
    Entity\File\Name,
    Entity\Folder\Identity as Folder,
    Exception\FileNotFound,
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Filesystem\MediaType\MediaType;
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class FileRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            FileRepositoryInterface::class,
            new FileRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($file = File::add(
                $identity,
                new Name('foo'),
                $this->createMock(Folder::class),
                new MediaType('application', 'octet-stream')
            ));

        $this->assertSame($file, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(FileNotFound::class);

        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->will($this->throwException(new EntityNotFound));

        $repository->get($identity);
    }

    public function testAdd()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $file = File::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Name('foo'),
            $this->createMock(Folder::class),
            new MediaType('application', 'octet-stream')
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($file);

        $this->assertSame($repository, $repository->add($file));
    }

    public function testRemove()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $file = File::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Name('foo'),
            $this->createMock(Folder::class),
            new MediaType('application', 'octet-stream')
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($file->identity())
            ->willReturn($file);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($file);

        $this->assertSame($repository, $repository->remove($file->identity()));
    }

    public function testHas()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->at(0))
            ->method('has')
            ->with($identity)
            ->willReturn(true);
        $inner
            ->expects($this->at(1))
            ->method('has')
            ->with($identity)
            ->willReturn(false);

        $this->assertTrue($repository->has($identity));
        $this->assertFalse($repository->has($identity));
    }

    public function testCount()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn($all = $this->createMock(SetInterface::class));
        $all
            ->expects($this->once())
            ->method('count')
            ->willReturn(42);

        $this->assertSame(42, $repository->count());
    }

    public function testAll()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $file = File::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Name('foo'),
                    $this->createMock(Folder::class),
                    new MediaType('application', 'octet-stream')
                )
            ));

        $all = $repository->all();

        $this->assertSame(File::class, (string) $all->type());
        $this->assertSame([$file], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new FileRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $file = File::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Name('foo'),
                    $this->createMock(Folder::class),
                    new MediaType('application', 'octet-stream')
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(File::class, (string) $matching->type());
        $this->assertSame([$file], $matching->toPrimitive());
    }
}
