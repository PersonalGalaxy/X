<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Listener;

use PersonalGalaxy\X\Component\RSS\Listener\BindArticleToSubscription;
use PersonalGalaxy\RSS\{
    Entity\Article,
    Entity\Article\Author,
    Entity\Article\Description,
    Entity\Article\Title,
    Entity\Subscription\Identity,
    Repository\ArticleRepository,
};
use Innmind\Url\UrlInterface;
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Neo4j\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class BindArticleToSubscriptionTest extends TestCase
{
    public function testInvokation()
    {
        $bind = new BindArticleToSubscription(
            $repository = $this->createMock(ArticleRepository::class),
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (subscription:RSS:Subscription { identity: {subscription} }), (article:RSS:Article { link: {article} }) WITH article, subscription CREATE (subscription)<-[:FETCHED_IN]-(article)' &&
                    $query->parameters()->get('article')->value() === 'localhost' &&
                    $query->parameters()->get('subscription')->value() === 'subscription uuid';
            }));
        $link = $this->createMock(UrlInterface::class);
        $link
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('localhost');
        $subscription = $this->createMock(Identity::class);
        $subscription
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('subscription uuid');
        $article = Article::fetch(
            $link,
            new Author('foo'),
            new Description('foo'),
            new Title('foo'),
            $this->createMock(PointInTimeInterface::class)
        );
        $article->bindTo($subscription);
        $repository
            ->expects($this->once())
            ->method('get')
            ->with($link)
            ->willReturn($article);

        $this->assertNull($bind($article->recordedEvents()->first()));
    }
}
