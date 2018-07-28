<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Type\Folder;

use PersonalGalaxy\X\Component\Files\Entity\Folder;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class IdentityType implements Type
{
    /**
     * {@inheritdoc}
     */
    public static function fromConfig(MapInterface $config, Types $types): Type
    {
        return new self;
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
        return new Folder($value);
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
        return Set::of('string', 'files_folder_identity');
    }
}
