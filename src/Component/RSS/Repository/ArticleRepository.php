<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Repository;

use PersonalGalaxy\RSS\{
    Repository\ArticleRepository as ArticleRepositoryInterface,
    Entity\Article,
    Exception\ArticleNotFound,
};
use Innmind\Url\UrlInterface;
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use Innmind\Specification\SpecificationInterface;

final class ArticleRepository implements ArticleRepositoryInterface
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(UrlInterface $link): Article
    {
        try {
            return $this->repository->get($link);
        } catch (EntityNotFound $e) {
            throw new ArticleNotFound('', 0, $e);
        }
    }

    public function add(Article $article): ArticleRepositoryInterface
    {
        $this->repository->add($article);

        return $this;
    }

    public function remove(UrlInterface $link): ArticleRepositoryInterface
    {
        $this->repository->remove(
            $this->get($link)
        );

        return $this;
    }

    public function has(UrlInterface $link): bool
    {
        return $this->repository->has($link);
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
            Article::class,
            ...$this->repository->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return Set::of(
            Article::class,
            ...$this->repository->matching($specification)
        );
    }
}
