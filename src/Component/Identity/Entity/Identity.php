<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Entity;

use PersonalGalaxy\Identity\Entity\Identity\Identity as IdentityInterface;
use PersonalGalaxy\Calendar\Entity\Agenda\User;
use PersonalGalaxy\RSS\Entity\Subscription\User as Subscription;
use Innmind\Neo4j\ONM\Identity\Uuid;
use Innmind\HttpAuthentication\Identity as Auth;

final class Identity extends Uuid implements IdentityInterface, User, Subscription, Auth
{
}
