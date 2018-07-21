<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Entity;

use PersonalGalaxy\Calendar\Entity\Agenda\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class Agenda extends Uuid implements Identity
{
}
