<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\{
    Web\Authentication\FormResolver,
    Component\Identity\Entity\Identity as Id,
    Web\Exception\UserNotFound,
};
use PersonalGalaxy\Identity\{
    Command\Identity\VerifyPassword,
    Entity\Identity,
    Entity\Identity\Email,
    Entity\Identity\Password,
    Repository\IdentityRepository,
    Exception\InvalidPassword,
};
use Innmind\HttpAuthentication\ViaForm\Resolver;
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Http\Message\{
    Form\Form,
    Form\Parameter\Parameter,
};
use Innmind\Immutable\{
    Map,
    Set,
};
use PHPUnit\Framework\TestCase;

class FormResolverTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Resolver::class,
            new FormResolver(
                $this->createMock(IdentityRepository::class),
                $this->createMock(CommandBusInterface::class)
            )
        );
    }

    public function testUserNotFound()
    {
        $resolve = new FormResolver(
            $repository = $this->createMock(IdentityRepository::class),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->willReturn(Set::of(Identity::class));
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
            $repository = $this->createMock(IdentityRepository::class),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->willReturn(Set::of(
                Identity::class,
                Identity::create(
                    new Id('037de1af-db12-4f6b-bc8f-ed395fdcfdfe'),
                    new Email('foo@bar.baz'),
                    new Password('foobarbaz')
                )
            ));
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
            $repository = $this->createMock(IdentityRepository::class),
            $commandBus = $this->createMock(CommandBusInterface::class)
        );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->willReturn(Set::of(
                Identity::class,
                Identity::create(
                    new Id('037de1af-db12-4f6b-bc8f-ed395fdcfdfe'),
                    new Email('foo@bar.baz'),
                    new Password('foobarbaz')
                )
            ));
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

