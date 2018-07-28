<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Repository;

use PersonalGalaxy\Files\{
    Repository\FileRepository as FileRepositoryInterface,
    Entity\File,
    Entity\File\Identity,
    Exception\FileNotFound,
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

final class FileRepository implements FileRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): File
    {
        try {
            return $this->repository->get($identity);
        } catch (EntityNotFound $e) {
            throw new FileNotFound('', 0, $e);
        }
    }

    public function add(File $file): FileRepositoryInterface
    {
        $this->repository->add($file);

        return $this;
    }

    public function remove(Identity $identity): FileRepositoryInterface
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
            File::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            File::class,
            ...$this->repository->matching($specification)
        );
    }
}
