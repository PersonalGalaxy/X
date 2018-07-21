<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Repository;

use PersonalGalaxy\X\Component\Calendar\{
    Repository\AgendaRepository,
    Entity\Agenda as Id,
};
use PersonalGalaxy\Calendar\{
    Repository\AgendaRepository as AgendaRepositoryInterface,
    Entity\Agenda,
    Entity\Agenda\User,
    Entity\Agenda\Name,
    Exception\AgendaNotFound,
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

class AgendaRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            AgendaRepositoryInterface::class,
            new AgendaRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new AgendaRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($agenda = Agenda::add(
                $identity,
                $this->createMock(User::class),
                new Name('foo')
            ));

        $this->assertSame($agenda, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(AgendaNotFound::class);

        $repository = new AgendaRepository(
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
        $repository = new AgendaRepository(
            $inner = $this->createMock(Repository::class)
        );
        $agenda = Agenda::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(User::class),
            new Name('foo')
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($agenda);

        $this->assertSame($repository, $repository->add($agenda));
    }

    public function testRemove()
    {
        $repository = new AgendaRepository(
            $inner = $this->createMock(Repository::class)
        );
        $agenda = Agenda::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(User::class),
            new Name('foo')
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($agenda->identity())
            ->willReturn($agenda);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($agenda);

        $this->assertSame($repository, $repository->remove($agenda->identity()));
    }

    public function testHas()
    {
        $repository = new AgendaRepository(
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
        $repository = new AgendaRepository(
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
        $repository = new AgendaRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $agenda = Agenda::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(User::class),
                    new Name('foo')
                )
            ));

        $all = $repository->all();

        $this->assertSame(Agenda::class, (string) $all->type());
        $this->assertSame([$agenda], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new AgendaRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $agenda = Agenda::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(User::class),
                    new Name('foo')
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Agenda::class, (string) $matching->type());
        $this->assertSame([$agenda], $matching->toPrimitive());
    }
}
