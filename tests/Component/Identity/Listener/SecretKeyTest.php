<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Listener;

use PersonalGalaxy\X\Component\Identity\Listener\SecretKey;
use PersonalGalaxy\Identity\{
    Event\Identity\TwoFactorAuthenticationWasEnabled,
    Entity\Identity\Identity,
    Entity\Identity\SecretKey as SecretKey2FA,
    Entity\Identity\RecoveryCode,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class SecretKeyTest extends TestCase
{
    public function testErrorWhenNotCollected()
    {
        $listener = new SecretKey;

        $this->expectException(\TypeError::class);

        $listener->key();
    }

    public function testKeyWhenCollected()
    {
        $listener = new SecretKey;

        $listener(new TwoFactorAuthenticationWasEnabled(
            $this->createMock(Identity::class),
            $key = new SecretKey2FA,
            Set::of(
                RecoveryCode::class,
                new RecoveryCode
            )
        ));

        $this->assertSame($key, $listener->key());
    }
}
