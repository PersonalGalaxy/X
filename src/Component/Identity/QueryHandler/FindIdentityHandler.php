<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\QueryHandler;

use PersonalGalaxy\X\{
    Component\Identity\Entity\Identity,
    Component\Identity\Query\FindIdentity,
};
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
};

final class FindIdentityHandler
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function __invoke(FindIdentity $wished): ?Identity
    {
        $result = $this->dbal->execute(
            (new Query)
                ->match('identity', ['User'])
                ->withProperty('email', '{email}')
                ->withParameter('email', (string) $wished->email())
                ->return('identity.identity as identity')
        );

        if ($result->rows()->size() === 0) {
            return null;
        }

        return new Identity($result->rows()->current()->value());
    }
}
