<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Type;

use PersonalGalaxy\X\Component\Identity\Type\PasswordType;
use PersonalGalaxy\Identity\Entity\Identity\Password;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class PasswordTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            PasswordType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            PasswordType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['password'],
            PasswordType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = PasswordType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );
        $password = new Password('foobarbaz');

        $this->assertSame((string) $password, $type->forDatabase($password));
    }

    public function testFromDatabase()
    {
        $type = PasswordType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Password::class, $type->fromDatabase('foobarbaz'));
        $this->assertSame('foobarbaz', (string) $type->fromDatabase('foobarbaz'));
    }
}
