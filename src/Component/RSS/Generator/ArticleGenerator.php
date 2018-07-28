<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Generator;

use PersonalGalaxy\X\Component\RSS\Entity\Article;
use Innmind\Neo4j\ONM\{
    Identity,
    Identity\Generator,
};
use Innmind\Immutable\Map;

final class ArticleGenerator implements Generator
{
    private $map;

    public function __construct()
    {
        $this->map = new Map('string', Article::class);
    }

    public function new(): Identity
    {
        throw new \LogicException('Urls can\'t be generated');
    }

    /**
     * {@inheritdoc}
     */
    public function knows($value): bool
    {
        return $this->map->contains($value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($value): Identity
    {
        return $this->map->get($value);
    }

    /**
     * Add the given identity to the known ones by this generator
     */
    public function add(Identity $identity): Generator
    {
        $this->map = $this->map->put((string) $identity, $identity);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function for($value): Identity
    {
        if ($this->knows($value)) {
            return $this->get($value);
        }

        $identity = new Article($value);
        $this->add($identity);

        return $identity;
    }
}
