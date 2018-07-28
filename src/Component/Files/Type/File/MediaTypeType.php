<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Type\File;

use Innmind\Filesystem\MediaType\MediaType;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class MediaTypeType implements Type
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
        return [$value->topLevel(), $value->subType(), $value->suffix()];
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return new MediaType($value[0], $value[1], $value[2]);
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
        return Set::of('string', 'files_file_media_type');
    }
}
