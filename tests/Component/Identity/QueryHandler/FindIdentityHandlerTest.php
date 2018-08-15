<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\QueryHandler;

use PersonalGalaxy\X\Component\Identity\{
    QueryHandler\FindIdentityHandler,
    Query\FindIdentity,
    Entity\Identity as Id,
};
use PersonalGalaxy\Identity\{
    Entity\Identity,
    Entity\Identity\Email,
    Entity\Identity\Password,
    Repository\IdentityRepository,
    Specification\Identity\Email as Spec,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class FindIdentityHandlerTest extends TestCase
{
    public function testFind()
    {
        $find = new FindIdentityHandler(
            $repository = $this->createMock(IdentityRepository::class)
        );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->with($this->callback(static function(Spec $spec): bool {
                return $spec->value() === 'foo@bar.baz';
            }))
            ->willReturn(Set::of(
                Identity::class,
                Identity::create(
                    $expected = new Id('03e53261-0812-4a81-88cc-5339c2232d7c'),
                    new Email('foo@bar.baz'),
                    new Password('foobarbaz')
                )
            ));

        $identity = $find(new FindIdentity(new Email('foo@bar.baz')));

        $this->assertSame($expected, $identity);
    }

    public function testReturnNullWhenNotFound()
    {
        $find = new FindIdentityHandler(
            $repository = $this->createMock(IdentityRepository::class)
        );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->with($this->callback(static function(Spec $spec): bool {
                return $spec->value() === 'foo@bar.baz';
            }))
            ->willReturn(Set::of(Identity::class));

        $this->assertNull($find(new FindIdentity(new Email('foo@bar.baz'))));
    }
}
