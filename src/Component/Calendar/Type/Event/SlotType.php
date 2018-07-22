<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Type\Event;

use PersonalGalaxy\X\Type\AbstractType;
use PersonalGalaxy\Calendar\Entity\Event\Slot;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class SlotType extends AbstractType implements Type
{
    private $date;

    public function __construct(string $class, Type $date)
    {
        parent::__construct($class);

        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromConfig(MapInterface $config, Types $types): Type
    {
        return new self(
            Slot::class,
            $types->build(
                'point_in_time',
                $config->clear()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forDatabase($value)
    {
        return [
            $this->date->forDatabase($value->start()),
            $this->date->forDatabase($value->end()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return $this
            ->reflection
            ->withProperty('start', $this->date->fromDatabase($value[0]))
            ->withProperty('end', $this->date->fromDatabase($value[1]))
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function identifiers(): SetInterface
    {
        return Set::of('string', 'calendar_event_slot');
    }
}
