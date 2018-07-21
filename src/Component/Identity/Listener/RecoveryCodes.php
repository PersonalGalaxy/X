<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Component\Identity\Listener;

use PersonalGalaxy\Identity\{
    Event\Identity\TwoFactorAuthenticationWasEnabled,
    Entity\Identity\RecoveryCode,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};

/**
 * Keep in memory the recovery codes so it can be used after the command bus
 * handling to be displayed to the UI (web or console) so the user can write
 * them down
 */
final class RecoveryCodes
{
    private $codes;

    public function __construct()
    {
        $this->codes = Set::of(RecoveryCode::class);
    }

    public function __invoke(TwoFactorAuthenticationWasEnabled $event): void
    {
        $this->codes = $event->recoveryCodes();
    }

    public function all(): SetInterface
    {
        return $this->codes;
    }
}
