<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Type;

use PersonalGalaxy\X\Component\Identity\Type\RecoveryCodeType;
use PersonalGalaxy\Identity\Entity\Identity\RecoveryCode;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class RecoveryCodeTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            RecoveryCodeType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            RecoveryCodeType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['recovery_code'],
            RecoveryCodeType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = RecoveryCodeType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );
        $code = new RecoveryCode;

        $this->assertSame((string) $code, $type->forDatabase($code));
    }

    public function testFromDatabase()
    {
        $type = RecoveryCodeType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(RecoveryCode::class, $type->fromDatabase('foobar'));
        $this->assertSame('foobar', (string) $type->fromDatabase('foobar'));
    }
}
