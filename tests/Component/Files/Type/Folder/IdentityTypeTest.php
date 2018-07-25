<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Type\Folder;

use PersonalGalaxy\X\Component\Files\{
    Type\Folder\IdentityType,
    Entity\Folder,
};
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class IdentityTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            IdentityType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            IdentityType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['files_folder_identity'],
            IdentityType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = IdentityType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame(
            '1aa4bb17-0ff1-41fe-873e-01d34acc0301',
            $type->forDatabase(new Folder('1aa4bb17-0ff1-41fe-873e-01d34acc0301'))
        );
    }

    public function testFromDatabase()
    {
        $type = IdentityType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(
            Folder::class,
            $type->fromDatabase('1aa4bb17-0ff1-41fe-873e-01d34acc0301')
        );
        $this->assertSame(
            '1aa4bb17-0ff1-41fe-873e-01d34acc0301',
            (string) $type->fromDatabase('1aa4bb17-0ff1-41fe-873e-01d34acc0301')
        );
    }
}
