<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Listener;

use PersonalGalaxy\RSS\Event\SubscriptionWasAdded;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindSubscriptionToIdentity
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(SubscriptionWasAdded $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('user', ['User'])
                ->withProperty('identity', '{user}')
                ->withParameter('user', (string) $event->user())
                ->match('subscription', ['RSS', 'Subscription'])
                ->withProperty('identity', '{subscription}')
                ->withParameter('subscription', (string) $event->identity())
                ->with('user', 'subscription')
                ->create('user')
                ->linkedTo('subscription')
                ->through('OWNED_BY', null, Relationship::LEFT)
        );
    }
}
