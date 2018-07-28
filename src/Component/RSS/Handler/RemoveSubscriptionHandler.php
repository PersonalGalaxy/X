<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Handler;

use PersonalGalaxy\RSS\{
    Handler\RemoveSubscriptionHandler as Handler,
    Command\RemoveSubscription,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
};

final class RemoveSubscriptionHandler
{
    private $handle;
    private $dbal;

    public function __construct(
        Handler $handle,
        Connection $dbal
    ) {
        $this->handle = $handle;
        $this->dbal = $dbal;
    }

    public function __invoke(RemoveSubscription $wished): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('subscription', ['RSS', 'Subscription'])
                ->withProperty('identity', '{identity}')
                ->withParameter('identity', (string) $wished->identity())
                ->with('subscription')
                ->match('subscription')
                ->linkedTo(null, ['User'])
                ->through('OWNED_BY', 'rel')
                ->match('subscription')
                ->linkedTo('articles', ['RSS', 'Article'])
                ->through('FETCHED_IN', 'relationships')
                ->with('collect(relationships) as relationships', 'rel')
                ->delete('rel')
                ->foreach('(relationship in relationships | DELETE relationship)')
        );
        ($this->handle)($wished);
    }
}
