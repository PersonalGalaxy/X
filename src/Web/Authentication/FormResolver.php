<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\Web\Exception\UserNotFound;
use PersonalGalaxy\Identity\{
    Entity\Identity\Email,
    Command\Identity\VerifyPassword,
    Specification\Identity\Email as Spec,
    Repository\IdentityRepository,
};
use Innmind\HttpAuthentication\{
    ViaForm\Resolver,
    Identity,
};
use Innmind\Http\Message\Form;
use Innmind\CommandBus\CommandBusInterface;

final class FormResolver implements Resolver
{
    private $repository;
    private $commandBus;

    public function __construct(
        IdentityRepository $repository,
        CommandBusInterface $commandBus
    ) {
        $this->repository = $repository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Form $form): Identity
    {
        $identities = $this->repository->matching(new Spec(new Email(
            $form->get('email')->value()
        )));

        if ($identities->size() !== 1) {
            throw new UserNotFound;
        }

        $identity = $identities->current();

        $this->commandBus->handle(new VerifyPassword(
            $identity->identity(),
            $form->get('password')->value()
        ));

        return $identity->identity();
    }
}
