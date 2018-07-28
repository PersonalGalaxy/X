<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Repository;

use PersonalGalaxy\X\Component\RSS\{
    Repository\ArticleRepository,
    Entity\Article as Id,
};
use PersonalGalaxy\RSS\{
    Repository\ArticleRepository as ArticleRepositoryInterface,
    Entity\Article,
    Entity\Article\Author,
    Entity\Article\Description,
    Entity\Article\Title,
    Exception\ArticleNotFound,
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class ArticleRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ArticleRepositoryInterface::class,
            new ArticleRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('localhost');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn($article = Article::fetch(
                $identity,
                new Author('foo'),
                new Description('foo'),
                new Title('foo'),
                $this->createMock(PointInTimeInterface::class)
            ));

        $this->assertSame($article, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownIdentity()
    {
        $this->expectException(ArticleNotFound::class);

        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('localhost');
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->will($this->throwException(new EntityNotFound));

        $repository->get($identity);
    }

    public function testAdd()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $article = Article::fetch(
            new Id('localhost'),
            new Author('foo'),
            new Description('foo'),
            new Title('foo'),
            $this->createMock(PointInTimeInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($article);

        $this->assertSame($repository, $repository->add($article));
    }

    public function testRemove()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $article = Article::fetch(
            new Id('localhost'),
            new Author('foo'),
            new Description('foo'),
            new Title('foo'),
            $this->createMock(PointInTimeInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('get')
            ->with($article->link())
            ->willReturn($article);
        $inner
            ->expects($this->once())
            ->method('remove')
            ->with($article);

        $this->assertSame($repository, $repository->remove($article->link()));
    }

    public function testHas()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $identity = new Id('localhost');
        $inner
            ->expects($this->at(0))
            ->method('has')
            ->with($identity)
            ->willReturn(true);
        $inner
            ->expects($this->at(1))
            ->method('has')
            ->with($identity)
            ->willReturn(false);

        $this->assertTrue($repository->has($identity));
        $this->assertFalse($repository->has($identity));
    }

    public function testCount()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn($all = $this->createMock(SetInterface::class));
        $all
            ->expects($this->once())
            ->method('count')
            ->willReturn(42);

        $this->assertSame(42, $repository->count());
    }

    public function testAll()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $inner
            ->expects($this->once())
            ->method('all')
            ->willReturn(Set::of(
                'object',
                $article = Article::fetch(
                    new Id('localhost'),
                    new Author('foo'),
                    new Description('foo'),
                    new Title('foo'),
                    $this->createMock(PointInTimeInterface::class)
                )
            ));

        $all = $repository->all();

        $this->assertSame(Article::class, (string) $all->type());
        $this->assertSame([$article], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new ArticleRepository(
            $inner = $this->createMock(Repository::class)
        );
        $spec = $this->createMock(SpecificationInterface::class);
        $inner
            ->expects($this->once())
            ->method('matching')
            ->with($spec)
            ->willReturn(Set::of(
                'object',
                $article = Article::fetch(
                    new Id('localhost'),
                    new Author('foo'),
                    new Description('foo'),
                    new Title('foo'),
                    $this->createMock(PointInTimeInterface::class)
                )
            ));

        $matching = $repository->matching($spec);

        $this->assertSame(Article::class, (string) $matching->type());
        $this->assertSame([$article], $matching->toPrimitive());
    }
}
