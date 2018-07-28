<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Repository;

use PersonalGalaxy\RSS\{
    Repository\SubscriptionRepository as SubscriptionRepositoryInterface,
    Entity\Subscription,
    Entity\Subscription\Identity,
    Exception\SubscriptionNotFound,
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use Innmind\Specification\SpecificationInterface;

final class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): Subscription
    {
        try {
            return $this->repository->get($identity);
        } catch (EntityNotFound $e) {
            throw new SubscriptionNotFound('', 0, $e);
        }
    }

    public function add(Subscription $subscription): SubscriptionRepositoryInterface
    {
        $this->repository->add($subscription);

        return $this;
    }

    public function remove(Identity $identity): SubscriptionRepositoryInterface
    {
        $this->repository->remove(
            $this->get($identity)
        );

        return $this;
    }

    public function has(Identity $identity): bool
    {
        return $this->repository->has($identity);
    }

    public function count(): int
    {
        return $this->repository->all()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function all(): SetInterface
    {
        return Set::of(
            Subscription::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Subscription::class,
            ...$this->repository->matching($specification)
        );
    }
}
