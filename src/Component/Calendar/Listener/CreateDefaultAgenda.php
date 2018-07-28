<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Listener;

use PersonalGalaxy\X\Component\Calendar\Entity\Agenda;
use PersonalGalaxy\Calendar\{
    Command\AddAgenda,
    Entity\Agenda\Name,
};
use PersonalGalaxy\Identity\Event\IdentityWasCreated;
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Neo4j\ONM\Manager;

final class CreateDefaultAgenda
{
    private $bus;
    private $manager;

    public function __construct(
        CommandBusInterface $bus,
        Manager $manager
    ) {
        $this->bus = $bus;
        $this->manager = $manager;
    }

    public function __invoke(IdentityWasCreated $event): void
    {
        $this->bus->handle(
            new AddAgenda(
                $this->manager->identities()->new(Agenda::class),
                $event->identity(),
                new Name('Agenda')
            )
        );
    }
}
