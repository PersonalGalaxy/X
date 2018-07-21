<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Listener;

use PersonalGalaxy\X\Component\Identity\Listener\RecoveryCodes;
use PersonalGalaxy\Identity\{
    Event\Identity\TwoFactorAuthenticationWasEnabled,
    Entity\Identity\Identity,
    Entity\Identity\SecretKey,
    Entity\Identity\RecoveryCode,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class RecoveryCodesTest extends TestCase
{
    public function testAllWhenNotCollected()
    {
        $codes = new RecoveryCodes;

        $this->assertInstanceOf(SetInterface::class, $codes->all());
        $this->assertSame(RecoveryCode::class, (string) $codes->all()->type());
        $this->assertCount(0, $codes->all());
    }

    public function testAllWhenCollected()
    {
        $codes = new RecoveryCodes;

        $codes(new TwoFactorAuthenticationWasEnabled(
            $this->createMock(Identity::class),
            new SecretKey,
            Set::of(
                RecoveryCode::class,
                $code = new RecoveryCode
            )
        ));

        $this->assertInstanceOf(SetInterface::class, $codes->all());
        $this->assertSame(RecoveryCode::class, (string) $codes->all()->type());
        $this->assertCount(1, $codes->all());
        $this->assertSame([$code], $codes->all()->toPrimitive());
    }
}
