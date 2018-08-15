<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\QueryHandler;

use PersonalGalaxy\X\{
    Component\Identity\Entity\Identity,
    Component\Identity\Query\FindIdentity,
};
use PersonalGalaxy\Identity\{
    Repository\IdentityRepository,
    Specification\Identity\Email,
};

final class FindIdentityHandler
{
    private $repository;

    public function __construct(IdentityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindIdentity $wished): ?Identity
    {
        $identities = $this
            ->repository
            ->matching(new Email($wished->email()));

        if ($identities->size() !== 1) {
            return null;
        }

        return $identities->current()->identity();
    }
}
