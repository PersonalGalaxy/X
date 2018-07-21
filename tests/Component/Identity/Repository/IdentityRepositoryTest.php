<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Repository;

use PersonalGalaxy\X\Component\Identity\{
    Repository\IdentityRepository,
    Entity\Identity as Id,
};
use PersonalGalaxy\Identity\{
    Repository\IdentityRepository as IdentityRepositoryInterface,
    Entity\Identity,
    Entity\Identity\Email,
    Entity\Identity\Password,
    Exception\IdentityNotFound,
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

class IdentityRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            IdentityRepositoryInterface::class,
            new IdentityRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $id = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($identity = Identity::create(
                $id,
                new Email('foo@bar.baz'),
                new Password('foobarbaz')
            ));

        $this->assertSame($identity, $repository->get($id));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $id = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($id)
            ->will($this->throwException(new EntityNotFound));

        $repository->get($id);
    }

    public function testAdd()
    {
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = Identity::create(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Email('foo@bar.baz'),
            new Password('foobarbaz')
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($identity);

        $this->assertSame($repository, $repository->add($identity));
    }

    public function testRemove()
    {
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = Identity::create(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            new Email('foo@bar.baz'),
            new Password('foobarbaz')
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity->identity())
            ->willReturn($identity);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($identity);

        $this->assertSame($repository, $repository->remove($identity->identity()));
    }

    public function testHas()
    {
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $id = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->at(0))
            ->method('has')
            ->with($id)
            ->willReturn(true);
        $inner
            ->expects($this->at(1))
            ->method('has')
            ->with($id)
            ->willReturn(false);

        $this->assertTrue($repository->has($id));
        $this->assertFalse($repository->has($id));
    }

    public function testCount()
    {
        $repository = new IdentityRepository(
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
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $identity = Identity::create(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Email('foo@bar.baz'),
                    new Password('foobarbaz')
                )
            ));

        $all = $repository->all();

        $this->assertSame(Identity::class, (string) $all->type());
        $this->assertSame([$identity], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new IdentityRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $identity = Identity::create(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    new Email('foo@bar.baz'),
                    new Password('foobarbaz')
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Identity::class, (string) $matching->type());
        $this->assertSame([$identity], $matching->toPrimitive());
    }
}
