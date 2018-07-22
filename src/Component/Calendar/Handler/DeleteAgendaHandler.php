<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Handler;

use PersonalGalaxy\Calendar\{
    Handler\DeleteAgendaHandler as Handler,
    Command\DeleteAgenda,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
};

final class DeleteAgendaHandler
{
    private $handle;
    private $dbal;

    public function __construct(Handler $handle, Connection $dbal)
    {
        $this->handle = $handle;
        $this->dbal = $dbal;
    }

    public function __invoke(DeleteAgenda $wished): void
    {
        $this->dbal->execute(
            (new Query)
                ->match(null, ['Calendar', 'Agenda'])
                ->withProperty('identity', '{identity}')
                ->withParameter('identity', (string) $wished->identity())
                ->linkedTo(null, ['User'])
                ->through('AGENDA_OF', 'rel')
                ->delete('rel')
        );
        ($this->handle)($wished);
    }
}
