<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X;

use PersonalGalaxy\X\QueryBus;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class QueryBusTest extends TestCase
{
    public function testInterface()
    {
        $object = new \stdClass;
        $query = new QueryBus(
            (new Map('string', 'callable'))
                ->put('stdClass', function($query) use ($object) {
                    $this->assertSame($object, $query);
                    $query->foo = 'bar';

                    return $query;
                })
        );

        $this->assertSame($object, $query($object));
        $this->assertSame('bar', $object->foo);
    }

    public function testThrowWhenInvalidMapKey()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, callable>');

        new QueryBus(new Map('int', 'callable'));
    }

    public function testThrowWhenInvalidMapValue()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, callable>');

        new QueryBus(new Map('string', 'object'));
    }

    public function testThrowWhenQueryHandlerNotFound()
    {
        $query = new QueryBus(new Map('string', 'callable'));

        $this->expectException(\Exception::class);

        $query(new \stdClass);
    }
}
