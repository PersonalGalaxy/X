<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Entity;

use PersonalGalaxy\Files\Entity\Folder\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class Folder extends Uuid implements Identity
{
    public function equals(Identity $folder): bool
    {
        return $this->value() === $folder->value();
    }
}
