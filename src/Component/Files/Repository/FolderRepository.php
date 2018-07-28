<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Repository;

use PersonalGalaxy\Files\{
    Repository\FolderRepository as FolderRepositoryInterface,
    Entity\Folder,
    Entity\Folder\Identity,
    Exception\FolderNotFound,
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

final class FolderRepository implements FolderRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): Folder
    {
        try {
            return $this->repository->get($identity);
        } catch (EntityNotFound $e) {
            throw new FolderNotFound($identity);
        }
    }

    public function add(Folder $folder): FolderRepositoryInterface
    {
        $this->repository->add($folder);

        return $this;
    }

    public function remove(Identity $identity): FolderRepositoryInterface
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
            Folder::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Folder::class,
            ...$this->repository->matching($specification)
        );
    }
}
