<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Command\Identity;

use PersonalGalaxy\X\{
    Command\Identity\Create,
    Component\Identity\Entity\Identity,
    Component\Identity\Listener\RecoveryCodes,
};
use PersonalGalaxy\Identity\{
    Command\CreateIdentity,
    Command\Identity\Enable2FA,
    Entity\Identity\Email,
    Entity\Identity\SecretKey,
    Entity\Identity\RecoveryCode,
    Event\Identity\TwoFactorAuthenticationWasEnabled,
    Exception\IdentityAlreadyExist,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Neo4j\ONM\{
    Manager,
    Identity\Generators,
    Identity\Generator,
};
use Innmind\Stream\{
    Readable,
    Writable,
    Selectable,
    Stream,
    Stream\Position,
    Stream\Position\Mode,
    Stream\Size,
};
use Innmind\Immutable\{
    Map,
    Str,
    Set
};
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Command::class,
            new Create(
                $this->createMock(CommandBusInterface::class),
                $this->createMock(Manager::class),
                new RecoveryCodes
            )
        );
    }

    public function testDefinition()
    {
        $command = new Create(
            $this->createMock(CommandBusInterface::class),
            $this->createMock(Manager::class),
            new RecoveryCodes
        );
        $expected = <<<DESC
identity:create email --enable-2fa

Create an identity that can connect to the app
DESC;

        $this->assertSame($expected, (string) $command);
    }

    public function testInvokation()
    {
        $create = new Create(
            $bus = $this->createMock(CommandBusInterface::class),
            $manager = $this->createMock(Manager::class),
            new RecoveryCodes
        );
        $manager
            ->expects($this->once())
            ->method('identities')
            ->willReturn(new Generators(
                (new Map('string', Generator::class))
                    ->put(Identity::class, $generator = $this->createMock(Generator::class))
            ));
        $generator
            ->expects($this->once())
            ->method('new')
            ->willReturn($identity = new Identity('9e20a588-6743-4703-88e9-238a64b9f4b7'));
        $bus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function(CreateIdentity $command) use ($identity): bool {
                return $command->identity() === $identity &&
                    (string) $command->email() === 'foo@bar.baz' &&
                    $command->password()->verify('foobarbaz');
            }));
        $env = $this->createMock(Environment::class);
        $env
            ->expects($this->any())
            ->method('input')
            ->willReturn(new class implements Readable, Selectable {
                private $firstCall = true;

                public function resource()
                {
                    return tmpfile();
                }

                public function read(int $length = null): Str
                {
                    if ($this->firstCall) {
                        $this->firstCall = false;

                        return Str::of("foobar\n");
                    }

                    return Str::of("foobarbaz\n");
                }

                public function readLine(): Str
                {
                }

                public function position(): Position
                {
                }

                public function seek(Position $position, Mode $mode = null): Stream
                {
                }

                public function rewind(): Stream
                {
                }

                public function end(): bool
                {
                }

                public function size(): Size
                {
                }

                public function knowsSize(): bool
                {
                }

                public function close(): Stream
                {
                }

                public function closed(): bool
                {
                }

                public function __toString(): string
                {
                }
            });
        $env
            ->expects($this->any())
            ->method('error')
            ->willReturn($error = $this->createMock(Writable::class));
        $error
            ->expects($this->once())
            ->method('write')
            ->with(Str::of("Password too short\n"));

        $this->assertNull($create(
            $env,
            new Arguments(
                (new Map('string', 'mixed'))
                    ->put('email', 'foo@bar.baz')
            ),
            new Options
        ));
    }

    public function testFailsWhenIdentityAlreadyExist()
    {
        $create = new Create(
            $bus = $this->createMock(CommandBusInterface::class),
            $manager = $this->createMock(Manager::class),
            new RecoveryCodes
        );
        $manager
            ->expects($this->once())
            ->method('identities')
            ->willReturn(new Generators(
                (new Map('string', Generator::class))
                    ->put(Identity::class, $generator = $this->createMock(Generator::class))
            ));
        $generator
            ->expects($this->once())
            ->method('new')
            ->willReturn($identity = new Identity('9e20a588-6743-4703-88e9-238a64b9f4b7'));
        $bus
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException(new IdentityAlreadyExist(new Email('foo@bar.baz'))));
        $env = $this->createMock(Environment::class);
        $env
            ->expects($this->any())
            ->method('input')
            ->willReturn(new class implements Readable, Selectable {
                public function resource()
                {
                    return tmpfile();
                }

                public function read(int $length = null): Str
                {
                    return Str::of("foobarbaz\n");
                }

                public function readLine(): Str
                {
                }

                public function position(): Position
                {
                }

                public function seek(Position $position, Mode $mode = null): Stream
                {
                }

                public function rewind(): Stream
                {
                }

                public function end(): bool
                {
                }

                public function size(): Size
                {
                }

                public function knowsSize(): bool
                {
                }

                public function close(): Stream
                {
                }

                public function closed(): bool
                {
                }

                public function __toString(): string
                {
                }
            });
        $env
            ->expects($this->once())
            ->method('exit')
            ->with(1);

        $this->assertNull($create(
            $env,
            new Arguments(
                (new Map('string', 'mixed'))
                    ->put('email', 'foo@bar.baz')
            ),
            new Options
        ));
    }

    public function testEnable2FA()
    {
        $create = new Create(
            $bus = $this->createMock(CommandBusInterface::class),
            $manager = $this->createMock(Manager::class),
            $codes = new RecoveryCodes
        );
        $codes(new TwoFactorAuthenticationWasEnabled(
            new Identity('9e20a588-6743-4703-88e9-238a64b9f4b7'),
            new SecretKey,
            Set::of(
                RecoveryCode::class,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode,
                new RecoveryCode
            )
        ));
        $manager
            ->expects($this->once())
            ->method('identities')
            ->willReturn(new Generators(
                (new Map('string', Generator::class))
                    ->put(Identity::class, $generator = $this->createMock(Generator::class))
            ));
        $generator
            ->expects($this->once())
            ->method('new')
            ->willReturn($identity = new Identity('9e20a588-6743-4703-88e9-238a64b9f4b7'));
        $bus
            ->expects($this->at(0))
            ->method('handle')
            ->with($this->callback(static function(CreateIdentity $command) use ($identity): bool {
                return $command->identity() === $identity &&
                    (string) $command->email() === 'foo@bar.baz' &&
                    $command->password()->verify('foobarbaz');
            }));
        $bus
            ->expects($this->at(1))
            ->method('handle')
            ->with($this->callback(static function(Enable2FA $command) use ($identity): bool {
                return $command->identity() === $identity;
            }));
        $env = $this->createMock(Environment::class);
        $env
            ->expects($this->any())
            ->method('input')
            ->willReturn(new class implements Readable, Selectable {
                public function resource()
                {
                    return tmpfile();
                }

                public function read(int $length = null): Str
                {
                    return Str::of("foobarbaz\n");
                }

                public function readLine(): Str
                {
                }

                public function position(): Position
                {
                }

                public function seek(Position $position, Mode $mode = null): Stream
                {
                }

                public function rewind(): Stream
                {
                }

                public function end(): bool
                {
                }

                public function size(): Size
                {
                }

                public function knowsSize(): bool
                {
                }

                public function close(): Stream
                {
                }

                public function closed(): bool
                {
                }

                public function __toString(): string
                {
                }
            });
        $env
            ->expects($this->any())
            ->method('output')
            ->willReturn($output = $this->createMock(Writable::class));
        $output
            ->expects($this->exactly(13))
            ->method('write');
        $output
            ->expects($this->at(2))
            ->method('write')
            ->with(Str::of("\nRecovery codes (to be kept in a safe place) : \n"));

        $this->assertNull($create(
            $env,
            new Arguments(
                (new Map('string', 'mixed'))
                    ->put('email', 'foo@bar.baz')
            ),
            new Options(
                (new Map('string', 'mixed'))
                    ->put('enable-2fa', true)
            )
        ));
    }
}
