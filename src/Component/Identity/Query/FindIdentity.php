<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Query;

use PersonalGalaxy\Identity\Entity\Identity\Email;

final class FindIdentity
{
    private $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
