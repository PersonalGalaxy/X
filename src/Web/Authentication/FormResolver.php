<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\{
    QueryBus,
    Component\Identity\Entity\Identity as Id,
    Component\Identity\Query\FindIdentity,
    Web\Exception\UserNotFound,
};
use PersonalGalaxy\Identity\{
    Entity\Identity\Email,
    Command\Identity\VerifyPassword,
};
use Innmind\HttpAuthentication\{
    ViaForm\Resolver,
    Identity,
};
use Innmind\Http\Message\Form;
use Innmind\CommandBus\CommandBusInterface;

final class FormResolver implements Resolver
{
    private $query;
    private $commandBus;

    public function __construct(QueryBus $query, CommandBusInterface $commandBus)
    {
        $this->query = $query;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Form $form): Identity
    {
        $identity = ($this->query)(new FindIdentity(new Email(
            $form->get('email')->value()
        )));

        if (!$identity instanceof Id) {
            throw new UserNotFound;
        }

        $this->commandBus->handle(new VerifyPassword(
            $identity,
            $form->get('password')->value()
        ));

        return $identity;
    }
}
