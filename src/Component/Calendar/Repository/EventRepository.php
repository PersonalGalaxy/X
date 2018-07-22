<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Repository;

use PersonalGalaxy\Calendar\{
    Repository\EventRepository as EventRepositoryInterface,
    Entity\Event,
    Entity\Event\Identity,
    Exception\EventNotFound,
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

final class EventRepository implements EventRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): Event
    {
        try {
            return $this->repository->get($identity);
        } catch (EntityNotFound $e) {
            throw new EventNotFound($identity);
        }
    }

    public function add(Event $event): EventRepositoryInterface
    {
        $this->repository->add($event);

        return $this;
    }

    public function remove(Identity $identity): EventRepositoryInterface
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
            Event::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Event::class,
            ...$this->repository->matching($specification)
        );
    }
}
