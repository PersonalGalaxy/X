<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\{
    Web\Authentication\FormResolver,
    QueryBus,
    Component\Identity\Query\FindIdentity,
    Component\Identity\Entity\Identity as Id,
    Web\Exception\UserNotFound,
};
use PersonalGalaxy\Identity\{
    Command\Identity\VerifyPassword,
    Exception\InvalidPassword,
};
use Innmind\HttpAuthentication\ViaForm\Resolver;
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Http\Message\{
    Form\Form,
    Form\Parameter\Parameter,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class FormResolverTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Resolver::class,
            new FormResolver(
                new QueryBus(new Map('string', 'callable')),
                $this->createMock(CommandBusInterface::class)
            )
        );
    }

    public function testUserNotFound()
    {
        $resolve = new FormResolver(
            new QueryBus(
                (new Map('string', 'callable'))
                    ->put(FindIdentity::class, function(){})
            ),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $commandBus
            ->expects($this->never())
            ->method('handle');

        $form = Form::of(
            new Parameter('email', 'foo@bar.baz'),
            new Parameter('password', 'foobarbaz')
        );

        $this->expectException(UserNotFound::class);

        $resolve($form);
    }

    public function testInvalidPassword()
    {
        $resolve = new FormResolver(
            new QueryBus(
                (new Map('string', 'callable'))
                    ->put(FindIdentity::class, function() {
                        return new Id('037de1af-db12-4f6b-bc8f-ed395fdcfdfe');
                    })
            ),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function(VerifyPassword $command): bool {
                return (string) $command->identity() === '037de1af-db12-4f6b-bc8f-ed395fdcfdfe' &&
                    $command->password() === 'foobarbaz';
            }))
            ->will($this->throwException(new InvalidPassword));

        $form = Form::of(
            new Parameter('email', 'foo@bar.baz'),
            new Parameter('password', 'foobarbaz')
        );

        $this->expectException(InvalidPassword::class);

        $resolve($form);
    }

    public function testReturnIdentity()
    {
        $resolve = new FormResolver(
            new QueryBus(
                (new Map('string', 'callable'))
                    ->put(FindIdentity::class, function() {
                        return new Id('037de1af-db12-4f6b-bc8f-ed395fdcfdfe');
                    })
            ),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function(VerifyPassword $command): bool {
                return (string) $command->identity() === '037de1af-db12-4f6b-bc8f-ed395fdcfdfe' &&
                    $command->password() === 'foobarbaz';
            }));

        $form = Form::of(
            new Parameter('email', 'foo@bar.baz'),
            new Parameter('password', 'foobarbaz')
        );

        $identity = $resolve($form);

        $this->assertInstanceOf(Id::class, $identity);
        $this->assertSame('037de1af-db12-4f6b-bc8f-ed395fdcfdfe', (string) $identity);
    }
}

