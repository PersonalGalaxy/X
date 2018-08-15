<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Query;

use PersonalGalaxy\X\Component\Identity\Query\FindIdentity;
use PersonalGalaxy\Identity\Entity\Identity\Email;
use PHPUnit\Framework\TestCase;

class FindIdentityTest extends TestCase
{
    public function testInterface()
    {
        $query = new FindIdentity(
            $email = new Email('foo@bar.baz')
        );

        $this->assertSame($email, $query->email());
    }
}
