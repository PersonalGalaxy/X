<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Repository;

use PersonalGalaxy\Identity\{
    Repository\IdentityRepository as IdentityRepositoryInterface,
    Entity\Identity,
    Exception\IdentityNotFound,
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

final class IdentityRepository implements IdentityRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity\Identity $id): Identity
    {
        try {
            return $this->repository->get($id);
        } catch (EntityNotFound $e) {
            throw new IdentityNotFound('', 0, $e);
        }
    }

    public function add(Identity $identity): IdentityRepositoryInterface
    {
        $this->repository->add($identity);

        return $this;
    }

    public function remove(Identity\Identity $id): IdentityRepositoryInterface
    {
        $this->repository->remove(
            $this->get($id)
        );

        return $this;
    }

    public function has(Identity\Identity $id): bool
    {
        return $this->repository->has($id);
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
            Identity::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Identity::class,
            ...$this->repository->matching($specification)
        );
    }
}
