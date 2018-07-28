<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Listener;

use PersonalGalaxy\X\Component\{
    Calendar\Listener\CreateDefaultAgenda,
    Calendar\Entity\Agenda,
    Identity\Entity\Identity as User,
};
use PersonalGalaxy\Calendar\Command\AddAgenda;
use PersonalGalaxy\Identity\{
    Event\IdentityWasCreated,
    Entity\Identity\Email,
};
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Neo4j\ONM\{
    Manager,
    Identity\Generators,
    Identity\Generator,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CreateDefaultAgendaTest extends TestCase
{
    public function testInvokation()
    {
        $create = new CreateDefaultAgenda(
            $bus = $this->createMock(CommandBusInterface::class),
            $manager = $this->createMock(Manager::class)
        );
        $identity = new Agenda('3b513761-1ea9-4bc1-b4fd-90bff35f19c9');
        $user = new User('3b513761-1ea9-4bc1-b4fd-90bff35f19c9');
        $bus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function(AddAgenda $command) use ($identity, $user): bool {
                return $command->identity() === $identity &&
                    $command->user() === $user &&
                    (string) $command->name() === 'Agenda';
            }));
        $manager
            ->expects($this->once())
            ->method('identities')
            ->willReturn(new Generators(
                (new Map('string', Generator::class))
                    ->put(Agenda::class, $generator = $this->createMock(Generator::class))
            ));
        $generator
            ->expects($this->once())
            ->method('new')
            ->willReturn($identity);

        $this->assertNull($create(new IdentityWasCreated(
            $user,
            new Email('foo@bar.baz')
        )));
    }
}
