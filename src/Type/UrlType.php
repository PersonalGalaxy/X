<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Type;

use Innmind\Url\{
    UrlInterface,
    Url,
};
use Innmind\Neo4j\ONM\{
    Type,
    Types,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
};

final class UrlType implements Type
{
    private $nullable;

    private function __construct(bool $nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromConfig(MapInterface $config, Types $types): Type
    {
        return new self($config->contains('nullable'));
    }

    /**
     * {@inheritdoc}
     */
    public function forDatabase($value)
    {
        if ($this->nullable && is_null($value)) {
            return;
        }

        if (!$value instanceof UrlInterface) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" must be an instance of UrlInterface',
                $value
            ));
        }

        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return Url::fromString($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * {@inheritdoc}
     */
    public static function identifiers(): SetInterface
    {
        return Set::of('string', 'url');
    }
}
