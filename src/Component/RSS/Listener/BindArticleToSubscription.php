<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Listener;

use PersonalGalaxy\RSS\{
    Repository\ArticleRepository,
    Event\ArticleWasFetched,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindArticleToSubscription
{
    private $repository;
    private $dbal;

    public function __construct(
        ArticleRepository $repository,
        Connection $dbal
    ) {
        $this->repository = $repository;
        $this->dbal = $dbal;
    }

    public function __invoke(ArticleWasFetched $event): void
    {
        $article = $this->repository->get($event->link());
        $this->dbal->execute(
            (new Query)
                ->match('subscription', ['RSS', 'Subscription'])
                ->withProperty('identity', '{subscription}')
                ->withParameter('subscription', (string) $article->subscription())
                ->match('article', ['RSS', 'Article'])
                ->withProperty('link', '{article}')
                ->withParameter('article', (string) $event->link())
                ->with('article', 'subscription')
                ->create('subscription')
                ->linkedTo('article')
                ->through('FETCHED_IN', null, Relationship::LEFT)
        );
    }
}
