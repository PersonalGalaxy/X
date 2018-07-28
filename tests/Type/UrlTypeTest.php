<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Type;

use PersonalGalaxy\X\Type\UrlType;
use Innmind\Url\Url;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};
use PHPUnit\Framework\TestCase;

class UrlTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            UrlType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertTrue(
            UrlType::fromConfig(
                (new Map('string', 'mixed'))
                    ->put('nullable', null),
                new Types
            )->isNullable()
        );
        $this->assertFalse(
            UrlType::fromConfig(
                new Map('string', 'mixed'),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['url'],
            UrlType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = UrlType::fromConfig(
            new Map('string', 'mixed'),
            new Types
        );

        $this->assertSame('localhost', $type->forDatabase(Url::fromString('localhost')));
    }

    public function testForDatabaseWhenNullable()
    {
        $type = UrlType::fromConfig(
            (new Map('string', 'mixed'))
                ->put('nullable', null),
            new Types
        );

        $this->assertSame(null, $type->forDatabase(null));
    }

    public function testThrowWhenForDatabaseNotAnUrl()
    {
        $this->expectException(InvalidArgumentException::class);

        $type = UrlType::fromConfig(
            new Map('string', 'mixed'),
            new Types
        );

        $type->forDatabase('localhost');
    }

    public function testFromDatabase()
    {
        $type = UrlType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Url::class, $type->fromDatabase('localhost'));
        $this->assertSame('localhost', (string) $type->fromDatabase('localhost'));
    }
}
