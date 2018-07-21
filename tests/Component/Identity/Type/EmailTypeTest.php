<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Type;

use PersonalGalaxy\X\Component\Identity\Type\EmailType;
use PersonalGalaxy\Identity\Entity\Identity\Email;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class EmailTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            EmailType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            EmailType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['email'],
            EmailType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = EmailType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo@bar.baz', $type->forDatabase(new Email('foo@bar.baz')));
    }

    public function testFromDatabase()
    {
        $type = EmailType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Email::class, $type->fromDatabase('foo@bar.baz'));
        $this->assertSame('foo@bar.baz', (string) $type->fromDatabase('foo@bar.baz'));
    }
}
