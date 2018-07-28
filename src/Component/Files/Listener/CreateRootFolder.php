<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\X\Component\Files\Entity\Folder;
use PersonalGalaxy\Identity\Event\IdentityWasCreated;
use Innmind\Neo4j\ONM\Manager;
use Innmind\Neo4j\DBAL\{
    Connection,
    Query\Query,
    Clause\Expression\Relationship,
};

final class CreateRootFolder
{
    private $dbal;
    private $manager;

    public function __construct(Connection $dbal, Manager $manager)
    {
        $this->dbal = $dbal;
        $this->manager = $manager;
    }

    public function __invoke(IdentityWasCreated $event): void
    {
        $this->dbal->execute(
            (new Query)
                ->match('user', ['User'])
                ->withProperty('identity', '{user}')
                ->withParameter('user', (string) $event->identity())
                ->create('user')
                ->linkedTo('root', ['Files', 'Folder', 'Root'])
                ->withProperty('identity', '{root}')
                ->withParameter(
                    'root',
                    (string) $this->manager->identities()->new(Folder::class)
                )
                ->through('OWNED_BY', null, Relationship::LEFT)
        );
    }
}
