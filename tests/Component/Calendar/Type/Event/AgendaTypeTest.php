<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Type\Event;

use PersonalGalaxy\X\Component\Calendar\{
    Type\Event\AgendaType,
    Entity\Agenda,
};
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class AgendaTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            AgendaType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            AgendaType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['calendar_event_agenda'],
            AgendaType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = AgendaType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame(
            '5a6b9979-2905-4ccb-8fe5-96b62c1b0876',
            $type->forDatabase(new Agenda('5a6b9979-2905-4ccb-8fe5-96b62c1b0876'))
        );
    }

    public function testFromDatabase()
    {
        $type = AgendaType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(
            Agenda::class,
            $type->fromDatabase('5a6b9979-2905-4ccb-8fe5-96b62c1b0876')
        );
        $this->assertSame(
            '5a6b9979-2905-4ccb-8fe5-96b62c1b0876',
            (string) $type->fromDatabase('5a6b9979-2905-4ccb-8fe5-96b62c1b0876')
        );
    }
}
