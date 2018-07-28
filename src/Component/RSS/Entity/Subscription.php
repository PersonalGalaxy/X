<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\RSS\Entity;

use PersonalGalaxy\RSS\Entity\Subscription\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;

final class Subscription extends Uuid implements Identity
{
}
