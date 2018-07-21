<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Type\Event;

use PersonalGalaxy\X\Component\Calendar\Type\Event\SlotType;
use PersonalGalaxy\Calendar\Entity\Event\Slot;
use Innmind\Neo4j\ONM\{
    Type,
    Type\PointInTimeType,
    Types,
};
use Innmind\TimeContinuum\PointInTime\Earth\PointInTime;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class SlotTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            SlotType::fromConfig(
                new Map('string', 'mixed'),
                new Types(PointInTimeType::class)
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            SlotType::fromConfig(
                new Map('string', 'mixed'),
                new Types(PointInTimeType::class)
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['calendar_event_slot'],
            SlotType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = SlotType::fromConfig(
            new Map('string', 'mixed'),
            new Types(PointInTimeType::class)
        );

        $this->assertSame(
            ['2018-01-01T00:00:00+00:00', '2018-12-31T00:00:00+00:00'],
            $type->forDatabase(new Slot(
                new PointInTime('2018-01-01T00:00:00Z'),
                new PointInTime('2018-12-31T00:00:00Z')
            ))
        );
    }

    public function testFromDatabase()
    {
        $type = SlotType::fromConfig(
            new Map('string', 'mixed'),
            new Types(PointInTimeType::class)
        );

        $this->assertInstanceOf(
            Slot::class,
            $type->fromDatabase([
                '2018-01-01T00:00:00+00:00',
                '2018-12-31T00:00:00+00:00',
            ])
        );
        $slot = $type->fromDatabase([
            '2018-01-01T00:00:00+00:00',
            '2018-12-31T00:00:00+00:00',
        ]);
        $this->assertTrue($slot->start()->equals(new PointInTime('2018-01-01T00:00:00+00:00')));
        $this->assertTrue($slot->end()->equals(new PointInTime('2018-12-31T00:00:00+00:00')));
    }
}

