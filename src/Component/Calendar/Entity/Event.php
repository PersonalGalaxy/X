<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Calendar\Entity;

use PersonalGalaxy\Calendar\Entity\Event\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class Event extends Uuid implements Identity
{
}
