<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Listener;

use PersonalGalaxy\Calendar\Event\AgendaWasAdded;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class BindAgendaToIdentity
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(AgendaWasAdded $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('user', ['User'])
                ->withProperty('identity', '{user}')
                ->match('agenda', ['Calendar', 'Agenda'])
                ->withProperty('identity', '{agenda}')
                ->with('user', 'agenda')
                ->create('agenda')
                ->linkedTo('user')
                ->through('AGENDA_OF', null, Relationship::RIGHT)
                ->withParameters([
                    'user' => (string) $event->user(),
                    'agenda' => (string) $event->identity(),
                ])
        );
    }
}
