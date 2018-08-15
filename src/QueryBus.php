<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X;

use Innmind\Immutable\MapInterface;

final class QueryBus
{
    private $map;

    public function __construct(MapInterface $map)
    {
        if (
            (string) $map->keyType() !== 'string' ||
            (string) $map->valueType() !== 'callable'
        ) {
            throw new \TypeError('Argument 1 must be of type MapInterface<string, callable>');
        }

        $this->map = $map;
    }

    /**
     * @return mixed
     */
    public function __invoke(object $query)
    {
        return $this->map->get(get_class($query))($query);
    }
}
