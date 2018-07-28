<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Entity;

use Innmind\Neo4j\ONM\Identity;
use Innmind\Url\{
    UrlInterface,
    Url,
    SchemeInterface,
    AuthorityInterface,
    PathInterface,
    QueryInterface,
    FragmentInterface,
};

final class Article implements Identity, UrlInterface
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = Url::fromString($url);
    }

    public function scheme(): SchemeInterface
    {
        return $this->url->scheme();
    }

    public function withScheme(SchemeInterface $scheme): UrlInterface
    {
        return new self(
            (string) $this->url->withScheme($scheme)
        );
    }

    public function authority(): AuthorityInterface
    {
        return $this->url->authority();
    }

    public function withAuthority(AuthorityInterface $authority): UrlInterface
    {
        return new self(
            (string) $this->url->withAuthority($authority)
        );
    }

    public function path(): PathInterface
    {
        return $this->url->path();
    }

    public function withPath(PathInterface $path): UrlInterface
    {
        return new self(
            (string) $this->url->withPath($path)
        );
    }

    public function query(): QueryInterface
    {
        return $this->url->query();
    }

    public function withQuery(QueryInterface $query): UrlInterface
    {
        return new self(
            (string) $this->url->withQuery($query)
        );
    }

    public function fragment(): FragmentInterface
    {
        return $this->url->fragment();
    }

    public function withFragment(FragmentInterface $fragment): UrlInterface
    {
        return new self(
            (string) $this->url->withFragment($fragment)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return (string) $this->url;
    }
}
