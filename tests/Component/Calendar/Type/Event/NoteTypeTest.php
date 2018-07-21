<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Type\Event;

use PersonalGalaxy\X\Component\Calendar\Type\Event\NoteType;
use PersonalGalaxy\Calendar\Entity\Event\Note;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class NoteTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            NoteType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            NoteType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['calendar_event_note'],
            NoteType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = NoteType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo', $type->forDatabase(new Note('foo')));
    }

    public function testFromDatabase()
    {
        $type = NoteType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Note::class, $type->fromDatabase('foo'));
        $this->assertSame('foo', (string) $type->fromDatabase('foo'));
    }
}
