<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS;

use PersonalGalaxy\X\Component\RSS\Entity\Article;
use PersonalGalaxy\RSS\UrlFactory as UrlFactoryInterface;
use Innmind\Url\UrlInterface;

final class UrlFactory implements UrlFactoryInterface
{
    public function __invoke(string $url): UrlInterface
    {
        return new Article($url);
    }
}
