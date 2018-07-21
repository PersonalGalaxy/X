<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Type;

use PersonalGalaxy\X\Type\AbstractType;
use PersonalGalaxy\Identity\Entity\Identity\RecoveryCode;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class RecoveryCodeType extends AbstractType implements Type
{
    /**
     * {@inheritdoc}
     */
    public static function fromConfig(MapInterface $config, Types $types): Type
    {
        return new self(RecoveryCode::class);
    }

    /**
     * {@inheritdoc}
     */
    public function forDatabase($value)
    {
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return $this
            ->reflection
            ->withProperty('value', $value)
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
        return Set::of('string', 'recovery_code');
    }
}
