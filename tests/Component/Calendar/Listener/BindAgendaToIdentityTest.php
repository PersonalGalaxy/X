<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Listener;

use PersonalGalaxy\X\Component\Calendar\Listener\BindAgendaToIdentity;
use PersonalGalaxy\Calendar\{
    Entity\Agenda\Identity,
    Entity\Agenda\User,
    Entity\Agenda\Name,
    Event\AgendaWasAdded,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindAgendaToIdentityTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindAgendaToIdentity(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (user:User { identity: {user} }), (agenda:Calendar:Agenda { identity: {agenda} }) WITH user, agenda CREATE (agenda)-[:AGENDA_OF]->(user)' &&
                    $query->parameters()->get('user')->value() === 'user uuid' &&
                    $query->parameters()->get('agenda')->value() === 'agenda uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('agenda uuid');
        $user = $this->createMock(User::class);
        $user
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('user uuid');

        $this->assertNull($bind(new AgendaWasAdded(
            $identity,
            $user,
            new Name('foo')
        )));
    }
}
