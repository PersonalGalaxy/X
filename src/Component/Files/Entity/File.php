<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Files\Entity;

use PersonalGalaxy\Files\Entity\File\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class File extends Uuid implements Identity
{
}
