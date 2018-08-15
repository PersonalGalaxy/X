<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\{
    Web\Authentication\ViaSession,
    Web\Authentication\Identity\Fresh,
    Component\Identity\Entity\Identity,
};
use Innmind\HttpAuthentication\Authenticator;
use Innmind\HttpSession\{
    Manager,
    Session,
    Session\Id,
    Session\Name,
};
use Innmind\Http\Message\ServerRequest;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ViaSessionTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Authenticator::class,
            new ViaSession(
                $this->createMock(Authenticator::class),
                $this->createMock(Manager::class)
            )
        );
    }

    public function testLoadIdentityFromSession()
    {
        $authenticate = new ViaSession(
            $inner = $this->createMock(Authenticator::class),
            $manager = $this->createMock(Manager::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $manager
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(false);
        $manager
            ->expects($this->once())
            ->method('start')
            ->with($request)
            ->willReturn($session = new Session(
                new Id('foo'),
                new Name('bar'),
                (new Map('string', 'mixed'))
                    ->put('identity', $expected = new Identity('aebc5eb4-1523-461d-bac7-5ee5b10a1459'))
            ));
        $manager
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn($session);
        $inner
            ->expects($this->never())
            ->method('__invoke');

        $this->assertSame($expected, $authenticate($request));
    }

    public function testLoadFromInnerAuthenticator()
    {
        $authenticate = new ViaSession(
            $inner = $this->createMock(Authenticator::class),
            $manager = $this->createMock(Manager::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $manager
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(false);
        $manager
            ->expects($this->once())
            ->method('start')
            ->with($request)
            ->willReturn($session = new Session(
                new Id('foo'),
                new Name('bar'),
                new Map('string', 'mixed')
            ));
        $manager
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn($session);
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($expected = new Identity('aebc5eb4-1523-461d-bac7-5ee5b10a1459'));

        $identity = $authenticate($request);

        $this->assertInstanceOf(Fresh::class, $identity);
        $this->assertSame($expected, $identity->original());
        $this->assertSame($expected, $session->get('identity'));
    }

    public function testCloseSessionOnAuthenticationFailure()
    {
        $authenticate = new ViaSession(
            $inner = $this->createMock(Authenticator::class),
            $manager = $this->createMock(Manager::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $manager
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(false);
        $manager
            ->expects($this->once())
            ->method('start')
            ->with($request)
            ->willReturn($session = new Session(
                new Id('foo'),
                new Name('bar'),
                new Map('string', 'mixed')
            ));
        $manager
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn($session);
        $manager
            ->expects($this->once())
            ->method('close')
            ->with($request);
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->will($this->throwException(new \Exception));

        try {
            $authenticate($request);
            $this->fail('it should throw');
        } catch (\Exception $e) {
            $this->assertFalse($session->has('identity'));
        }
    }
}
