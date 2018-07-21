<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Command\Identity;

use PersonalGalaxy\X\Component\Identity\Entity\Identity;
use PersonalGalaxy\Identity\{
    Command\CreateIdentity,
    Entity\Identity\Email,
    Entity\Identity\Password,
    Exception\DomainException,
    Exception\IdentityAlreadyExist,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
    Question\Question,
};
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Neo4j\ONM\Manager;
use Innmind\Immutable\Str;

final class Create implements Command
{
    private $bus;
    private $manager;

    public function __construct(CommandBusInterface $bus, Manager $manager)
    {
        $this->bus = $bus;
        $this->manager = $manager;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        $ask = Question::hiddenResponse('password:');
        do {
            $invalidPassword = false;

            try {
                $password = new Password((string) $ask($env->input(), $env->output()));
            } catch (DomainException $e) {
                $invalidPassword = true;
                $env->error()->write(Str::of("Password too short\n"));
            }
        } while ($invalidPassword);

        try {
            $this->bus->handle(new CreateIdentity(
                $this->manager->identities()->new(Identity::class),
                new Email($arguments->get('email')),
                $password
            ));
        } catch (IdentityAlreadyExist $e) {
            $env->output()->write(Str::of("{$arguments->get('email')} alreay exist\n"));
            $env->exit(1);

            return;
        }
    }

    public function __toString(): string
    {
        return <<<DESC
identity:create email

Create an identity that can connect to the app
DESC;
    }
}
