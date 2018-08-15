<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Listener;

use PersonalGalaxy\Identity\{
    Event\Identity\TwoFactorAuthenticationWasEnabled,
    Entity\Identity\SecretKey as SecretKey2FA,
};

/**
 * Keep in memory the secret key so it can be used after the command bus
 * handling to be displayed to the UI (web or console) so the user can write
 * it down or be used to generate a QR code
 */
final class SecretKey
{
    private $key;

    public function __invoke(TwoFactorAuthenticationWasEnabled $event): void
    {
        $this->key = $event->secretKey();
    }

    public function key(): SecretKey2FA
    {
        return $this->key;
    }
}
