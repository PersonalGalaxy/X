<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Type\File;

use PersonalGalaxy\X\Component\Files\Type\File\MediaTypeType;
use Innmind\Filesystem\MediaType\MediaType;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class MediaTypeTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            MediaTypeType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            MediaTypeType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['files_file_media_type'],
            MediaTypeType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = MediaTypeType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame(
            ['application', 'octet-stream', 'foo'],
            $type->forDatabase(new MediaType('application', 'octet-stream', 'foo'))
        );
    }

    public function testFromDatabase()
    {
        $type = MediaTypeType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(
            MediaType::class,
            $type->fromDatabase(['application', 'octet-stream', 'foo'])
        );
        $this->assertSame(
            'application/octet-stream+foo',
            (string) $type->fromDatabase(['application', 'octet-stream', 'foo'])
        );
    }
}
