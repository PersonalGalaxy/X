<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Type;

use PersonalGalaxy\X\Component\Identity\Type\SecretKeyType;
use PersonalGalaxy\Identity\Entity\Identity\SecretKey;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class SecretKeyTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            SecretKeyType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNullable()
    {
        $this->assertTrue(
            SecretKeyType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['secret_key'],
            SecretKeyType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = SecretKeyType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );
        $key = new SecretKey;

        $this->assertSame((string) $key, $type->forDatabase($key));
    }

    public function testFromDatabase()
    {
        $type = SecretKeyType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );
        $code = \bin2hex(\random_bytes(20));

        $this->assertInstanceOf(SecretKey::class, $type->fromDatabase($code));
        $this->assertSame($code, (string) $type->fromDatabase($code));
    }
}
