<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Handler;

use PersonalGalaxy\X\Component\RSS\Handler\RemoveSubscriptionHandler;
use PersonalGalaxy\RSS\{
    Handler\RemoveSubscriptionHandler as Handler,
    Command\RemoveSubscription,
    Repository\SubscriptionRepository,
    Entity\Subscription,
    Entity\Subscription\Identity,
    Entity\Subscription\User,
    Entity\Subscription\Name,
};
use Innmind\Url\UrlInterface;
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class RemoveSubscriptionHandlerTest extends TestCase
{
    public function testInvokation()
    {
        $handle = new RemoveSubscriptionHandler(
            new Handler(
                $repository = $this->createMock(SubscriptionRepository::class)
            ),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (subscription:RSS:Subscription { identity: {identity} }) WITH subscription MATCH (subscription)-[rel:OWNED_BY]-(:User), (subscription)-[relationships:FETCHED_IN]-(articles:RSS:Article) WITH collect(relationships) as relationships, rel DELETE rel FOREACH (relationship in relationships | DELETE relationship)' &&
                    $query->parameters()->get('identity')->value() === 'subscription uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('subscription uuid');
        $repository
            ->expects($this->once())
            ->method('get')
            ->willReturn(Subscription::add(
                $identity,
                $this->createMock(User::class),
                new Name('foo'),
                $this->createMock(UrlInterface::class)
            ));

        $this->assertNull($handle(new RemoveSubscription($identity)));
    }
}
