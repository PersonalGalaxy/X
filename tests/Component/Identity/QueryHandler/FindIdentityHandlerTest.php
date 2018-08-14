<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\QueryHandler;

use PersonalGalaxy\X\Component\Identity\{
    QueryHandler\FindIdentityHandler,
    Query\FindIdentity,
    Entity\Identity,
};
use PersonalGalaxy\Identity\Entity\Identity\Email;
use Innmind\Neo4j\DBAL\{
    Connection,
    Result\Result,
    Result\Node,
    Result\Relationship,
    Result\Row,
};
use Innmind\Immutable\{
    Map,
    Stream,
};
use PHPUnit\Framework\TestCase;

class FindIdentityHandlerTest extends TestCase
{
    public function testFind()
    {
        $find = new FindIdentityHandler(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (identity:User { email: {email} }) RETURN identity.identity as identity' &&
                    $query->parameters()->get('email')->value() === 'foo@bar.baz';
            }))
            ->willReturn(new Result(
                new Map('int', Node::class),
                new Map('int', Relationship::class),
                Stream::of(Row::class, new Row\Row('identity', '03e53261-0812-4a81-88cc-5339c2232d7c'))
            ));

        $identity = $find(new FindIdentity(new Email('foo@bar.baz')));

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertSame('03e53261-0812-4a81-88cc-5339c2232d7c', (string) $identity);
    }

    public function testReturnNullWhenNotFound()
    {
        $find = new FindIdentityHandler(
            $dbal = $this->createMock(Connection::class)
        );
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (identity:User { email: {email} }) RETURN identity.identity as identity' &&
                    $query->parameters()->get('email')->value() === 'foo@bar.baz';
            }))
            ->willReturn(new Result(
                new Map('int', Node::class),
                new Map('int', Relationship::class),
                Stream::of(Row::class)
            ));

        $this->assertNull($find(new FindIdentity(new Email('foo@bar.baz'))));
    }
}
