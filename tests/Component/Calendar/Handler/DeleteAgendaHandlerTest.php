<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Handler;

use PersonalGalaxy\X\Component\Calendar\Handler\DeleteAgendaHandler;
use PersonalGalaxy\Calendar\{
    Handler\DeleteAgendaHandler as Handler,
    Handler\CancelEventHandler,
    Command\DeleteAgenda,
    Repository\AgendaRepository,
    Repository\EventRepository,
    Entity\Agenda,
    Entity\Agenda\Identity,
    Entity\Agenda\User,
    Entity\Agenda\Name,
};
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class DeleteAgendaHandlerTest extends TestCase
{
    public function testInvokation()
    {
        $handle = new DeleteAgendaHandler(
            new Handler(
                $agendas = $this->createMock(AgendaRepository::class),
                $events = $this->createMock(EventRepository::class),
                new CancelEventHandler($events)
            ),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (:Calendar:Agenda { identity: {identity} })-[rel:AGENDA_OF]-(:User) DELETE rel' &&
                    $query->parameters()->get('identity')->value() === 'agenda uuid';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->any())
            ->method('__toString')
            ->willReturn('agenda uuid');
        $agendas
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn(Agenda::add(
                $identity,
                $this->createMock(User::class),
                new Name('foo')
            ));

        $this->assertNull($handle(new DeleteAgenda($identity)));
    }
}
