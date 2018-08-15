<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\RequestHandler;

use PersonalGalaxy\X\Web\{
    RequestHandler\SendSessionCookie,
    Authentication\Identity\Fresh,
};
use Innmind\HttpFramework\RequestHandler;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\HttpAuthentication\{
    ViaStorage\Storage,
    Identity,
};
use Innmind\HttpSession\{
    Manager,
    Session,
    Session\Id,
    Session\Name,
};
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    PointInTime\Earth\PointInTime,
};
use Innmind\Url\Url;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class SendSessionCookieTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            RequestHandler::class,
            new SendSessionCookie(
                $this->createMock(RequestHandler::class),
                $this->createMock(Storage::class),
                $this->createMock(Manager::class),
                $this->createMock(TimeContinuumInterface::class)
            )
        );
    }

    public function testDoesNothingWhenNoIdentity()
    {
        $send = new SendSessionCookie(
            $inner = $this->createMock(RequestHandler::class),
            $storage = $this->createMock(Storage::class),
            $manager = $this->createMock(Manager::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($response = $this->createMock(Response::class));
        $storage
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(false);

        $this->assertSame($response, $send($request));
    }

    public function testDoesNothingWhenNotFreshIdentity()
    {
        $send = new SendSessionCookie(
            $inner = $this->createMock(RequestHandler::class),
            $storage = $this->createMock(Storage::class),
            $manager = $this->createMock(Manager::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($response = $this->createMock(Response::class));
        $storage
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(true);
        $storage
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn($this->createMock(Identity::class));

        $this->assertSame($response, $send($request));
    }

    public function testSendCookie()
    {
        $send = new SendSessionCookie(
            $inner = $this->createMock(RequestHandler::class),
            $storage = $this->createMock(Storage::class),
            $manager = $this->createMock(Manager::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $request
            ->expects($this->once())
            ->method('url')
            ->willReturn(Url::fromString('/foo/bar'));
        $inner
            ->expects($this->never())
            ->method('__invoke');
        $storage
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(true);
        $storage
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn(new Fresh($this->createMock(Identity::class)));
        $manager
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn(new Session(
                new Id('foo'),
                new Name('bar'),
                new Map('string', 'mixed')
            ));
        $clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(new PointInTime('2018-11-30T12:13:14+0100'));

        $response = $send($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->statusCode()->value());
        $this->assertSame(
            'Location : /foo/bar',
            (string) $response->headers()->get('Location')
        );
        $this->assertSame(
            'Set-Cookie : bar=foo; HttpOnly; SameSite=Strict; Secure; Expires="Thu, 28 Feb 2019 12:13:14 +0100"',
            (string) $response->headers()->get('Set-Cookie')
        );
    }
}
