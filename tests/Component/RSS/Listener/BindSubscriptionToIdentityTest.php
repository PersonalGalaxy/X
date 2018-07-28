<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Listener;

use PersonalGalaxy\X\Component\RSS\Listener\BindSubscriptionToIdentity;
use PersonalGalaxy\RSS\{
    Event\SubscriptionWasAdded,
    Entity\Subscription\Identity,
    Entity\Subscription\User,
    Entity\Subscription\Name,
};
use Innmind\Url\UrlInterface;
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindSubscriptionToIdentityTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindSubscriptionToIdentity(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (user:User { identity: {user} }), (subscription:RSS:Subscription { identity: {subscription} }) WITH user, subscription CREATE (user)<-[:OWNED_BY]-(subscription)' &&
                    $query->parameters()->get('user')->value() === 'user uuid' &&
                    $query->parameters()->get('subscription')->value() === 'subscription uuid';
            }));
        $user = $this->createMock(User::class);
        $user
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('user uuid');
        $subscription = $this->createMock(Identity::class);
        $subscription
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('subscription uuid');

        $this->assertNull($bind(new SubscriptionWasAdded(
            $subscription,
            $user,
            new Name('foo'),
            $this->createMock(UrlInterface::class)
        )));
    }
}
