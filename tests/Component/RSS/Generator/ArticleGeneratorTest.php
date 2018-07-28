<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Generator;

use PersonalGalaxy\X\Component\RSS\{
    Generator\ArticleGenerator,
    Entity\Article,
};
use Innmind\Neo4j\ONM\Identity\Generator;
use PHPUnit\Framework\TestCase;

class ArticleGeneratorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Generator::class, new ArticleGenerator);
    }

    public function testThrowWhenTryingToGenerateIdentity()
    {
        $this->expectException(\LogicException::class);

        (new ArticleGenerator)->new();
    }

    public function testAdd()
    {
        $generator = new ArticleGenerator;

        $this->assertSame($generator, $generator->add(new Article('localhost')));
    }

    public function testKnows()
    {
        $generator = new ArticleGenerator;

        $this->assertFalse($generator->knows('localhost'));
        $generator->add(new Article('localhost'));
        $this->assertTrue($generator->knows('localhost'));
    }

    public function testGet()
    {
        $generator = new ArticleGenerator;
        $article = new Article('localhost');
        $generator->add($article);

        $this->assertSame($article, $generator->get('localhost'));
    }

    public function testFor()
    {
        $generator = new ArticleGenerator;

        $article = $generator->for('localhost');

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame('localhost', (string) $article);
        $this->assertSame($article, $generator->for('localhost'));
    }
}
