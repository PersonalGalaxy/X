<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Entity;

use PersonalGalaxy\Identity\Entity\Identity\Identity as IdentityInterface;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class Identity extends Uuid implements IdentityInterface
{
}
