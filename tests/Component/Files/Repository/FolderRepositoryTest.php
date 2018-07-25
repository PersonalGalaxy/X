<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Repository;

use PersonalGalaxy\X\Component\Files\{
    Repository\FolderRepository,
    Entity\Folder as Id,
};
use PersonalGalaxy\Files\{
    Repository\FolderRepository as FolderRepositoryInterface,
    Entity\Folder,
    Entity\Folder\Name,
    Exception\FolderNotFound,
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class FolderRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            FolderRepositoryInterface::class,
            new FolderRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new FolderRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($folder = Folder::add(
                $identity,
                new Name('foo'),
                $this->createMock(Folder\Identity::class)
            ));

        $this->assertSame($folder, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(FolderNotFound::class);

        $repository = new FolderRepository(
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
        $repository = new FolderRepository(
            $inner = $this->createMock(Repository::class)
        );
        $folder = Folder::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Name('foo'),
            $this->createMock(Folder\Identity::class)
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($folder);

        $this->assertSame($repository, $repository->add($folder));
    }

    public function testRemove()
    {
        $repository = new FolderRepository(
            $inner = $this->createMock(Repository::class)
        );
        $folder = Folder::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Name('foo'),
            $this->createMock(Folder\Identity::class)
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($folder->identity())
            ->willReturn($folder);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($folder);

        $this->assertSame($repository, $repository->remove($folder->identity()));
    }

    public function testHas()
    {
        $repository = new FolderRepository(
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
        $repository = new FolderRepository(
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
        $repository = new FolderRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $folder = Folder::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Name('foo'),
                    $this->createMock(Folder\Identity::class)
                )
            ));

        $all = $repository->all();

        $this->assertSame(Folder::class, (string) $all->type());
        $this->assertSame([$folder], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new FolderRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $folder = Folder::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Name('foo'),
                    $this->createMock(Folder\Identity::class)
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Folder::class, (string) $matching->type());
        $this->assertSame([$folder], $matching->toPrimitive());
    }
}
