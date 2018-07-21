<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X;

use Innmind\Neo4j\ONM\Manager;

final class Repository
{
    public static function build(
        Manager $manager,
        string $repository,
        string $entity
    ): object {
        return new $repository(
            $manager->repository($entity)
        );
    }
}
