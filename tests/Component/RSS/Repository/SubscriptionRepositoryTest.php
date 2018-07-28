<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Repository;

use PersonalGalaxy\X\Component\RSS\{
    Repository\SubscriptionRepository,
    Entity\Subscription as Id,
};
use PersonalGalaxy\RSS\{
    Repository\SubscriptionRepository as SubscriptionRepositoryInterface,
    Entity\Subscription,
    Entity\Subscription\Name,
    Entity\Subscription\User,
    Exception\SubscriptionNotFound,
};
use Innmind\Url\UrlInterface;
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

class SubscriptionRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            SubscriptionRepositoryInterface::class,
            new SubscriptionRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new SubscriptionRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($subscription = Subscription::add(
                $identity,
                $this->createMock(User::class),
                new Name('foo'),
                $this->createMock(UrlInterface::class)
            ));

        $this->assertSame($subscription, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(SubscriptionNotFound::class);

        $repository = new SubscriptionRepository(
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
        $repository = new SubscriptionRepository(
            $inner = $this->createMock(Repository::class)
        );
        $subscription = Subscription::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(User::class),
            new Name('foo'),
            $this->createMock(UrlInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($subscription);

        $this->assertSame($repository, $repository->add($subscription));
    }

    public function testRemove()
    {
        $repository = new SubscriptionRepository(
            $inner = $this->createMock(Repository::class)
        );
        $subscription = Subscription::add(
            new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
            $this->createMock(User::class),
            new Name('foo'),
            $this->createMock(UrlInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($subscription->identity())
            ->willReturn($subscription);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($subscription);

        $this->assertSame($repository, $repository->remove($subscription->identity()));
    }

    public function testHas()
    {
        $repository = new SubscriptionRepository(
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
        $repository = new SubscriptionRepository(
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
        $repository = new SubscriptionRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $subscription = Subscription::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(User::class),
                    new Name('foo'),
                    $this->createMock(UrlInterface::class)
                )
            ));

        $all = $repository->all();

        $this->assertSame(Subscription::class, (string) $all->type());
        $this->assertSame([$subscription], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new SubscriptionRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $subscription = Subscription::add(
                    new Id('3ab9abc9-43d1-429b-a9df-482d471aa3b0'),
                    $this->createMock(User::class),
                    new Name('foo'),
                    $this->createMock(UrlInterface::class)
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Subscription::class, (string) $matching->type());
        $this->assertSame([$subscription], $matching->toPrimitive());
    }
}
