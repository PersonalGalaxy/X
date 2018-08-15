<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Authentication\Identity;

use Innmind\HttpAuthentication\Identity;

/**
 * Means the identity has been identified in the current request
 *
 * This class should obviously never be persisted to any storage such as a session
 */
final class Fresh implements Identity
{
    private $original;

    public function __construct(Identity $original)
    {
        $this->original = $original;
    }

    public function original(): Identity
    {
        return $this->original;
    }

    public function __toString(): string
    {
        return (string) $this->original;
    }
}
