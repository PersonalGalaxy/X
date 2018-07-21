<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Repository;

use PersonalGalaxy\Calendar\{
    Repository\AgendaRepository as AgendaRepositoryInterface,
    Entity\Agenda,
    Entity\Agenda\Identity,
    Exception\AgendaNotFound,
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

final class AgendaRepository implements AgendaRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): Agenda
    {
        try {
            return $this->repository->get($identity);
        } catch (EntityNotFound $e) {
            throw new AgendaNotFound($identity);
        }
    }

    public function add(Agenda $agenda): AgendaRepositoryInterface
    {
        $this->repository->add($agenda);

        return $this;
    }

    public function remove(Identity $identity): AgendaRepositoryInterface
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
            Agenda::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Agenda::class,
            ...$this->repository->matching($specification)
        );
    }
}
