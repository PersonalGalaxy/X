<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS;

use PersonalGalaxy\X\Component\RSS\{
    UrlFactory,
    Entity\Article,
};
use PersonalGalaxy\RSS\UrlFactory as UrlFactoryInterface;
use PHPUnit\Framework\TestCase;

class UrlFactoryTest extends TestCase
{
    public function testInterface()
    {
        $factory = new UrlFactory;

        $this->assertInstanceOf(UrlFactoryInterface::class, $factory);
        $this->assertInstanceOf(Article::class, $factory('http://localhost'));
    }
}
