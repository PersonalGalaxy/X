<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Repository;

use PersonalGalaxy\X\Component\Calendar\{
    Repository\EventRepository,
    Entity\Event as Id,
};
use PersonalGalaxy\Calendar\{
    Repository\EventRepository as EventRepositoryInterface,
    Entity\Event,
    Entity\Event\Name,
    Entity\Event\Slot,
    Entity\Agenda\Identity,
    Exception\EventNotFound,
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class EventRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            EventRepositoryInterface::class,
            new EventRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new EventRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($event = Event::add(
                $identity,
                $this->createMock(Identity::class),
                new Name('foo'),
                new Slot(
                    $this->createMock(PointInTimeInterface::class),
                    $this->createMock(PointInTimeInterface::class)
                )
            ));

        $this->assertSame($event, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(EventNotFound::class);

        $repository = new EventRepository(
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
        $repository = new EventRepository(
            $inner = $this->createMock(Repository::class)
        );
        $event = Event::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(Identity::class),
            new Name('foo'),
            new Slot(
                $this->createMock(PointInTimeInterface::class),
                $this->createMock(PointInTimeInterface::class)
            )
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($event);

        $this->assertSame($repository, $repository->add($event));
    }

    public function testRemove()
    {
        $repository = new EventRepository(
            $inner = $this->createMock(Repository::class)
        );
        $event = Event::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(Identity::class),
            new Name('foo'),
            new Slot(
                $this->createMock(PointInTimeInterface::class),
                $this->createMock(PointInTimeInterface::class)
            )
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($event->identity())
            ->willReturn($event);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($event);

        $this->assertSame($repository, $repository->remove($event->identity()));
    }

    public function testHas()
    {
        $repository = new EventRepository(
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
        $repository = new EventRepository(
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
        $repository = new EventRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $event = Event::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(Identity::class),
                    new Name('foo'),
                    new Slot(
                        $this->createMock(PointInTimeInterface::class),
                        $this->createMock(PointInTimeInterface::class)
                    )
                )
            ));

        $all = $repository->all();

        $this->assertSame(Event::class, (string) $all->type());
        $this->assertSame([$event], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new EventRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $event = Event::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(Identity::class),
                    new Name('foo'),
                    new Slot(
                        $this->createMock(PointInTimeInterface::class),
                        $this->createMock(PointInTimeInterface::class)
                    )
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Event::class, (string) $matching->type());
        $this->assertSame([$event], $matching->toPrimitive());
    }
}
