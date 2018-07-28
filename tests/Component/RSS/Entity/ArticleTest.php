<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Entity;

use PersonalGalaxy\X\Component\RSS\Entity\Article;
use Innmind\Url\UrlInterface;
use Innmind\Neo4j\ONM\Identity;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Article('http://localhost');

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertInstanceOf(UrlInterface::class, $identity);
    }
}
