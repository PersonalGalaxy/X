<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Controller;

use PersonalGalaxy\X\Web\Controller\Logout;
use Innmind\HttpFramework\Controller;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\HttpSession\{
    Manager,
    Session,
    Session\Id,
    Session\Name,
};
use Innmind\Router\{
    UrlGenerator,
    Route,
};
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    PointInTime\Earth\PointInTime,
};
use Innmind\Url\Url;
use Innmind\Immutable\{
    Map,
    Str,
};
use PHPUnit\Framework\TestCase;

class LogoutTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Controller::class,
            new Logout(
                $this->createMock(Manager::class),
                $this->createMock(UrlGenerator::class),
                $this->createMock(TimeContinuumInterface::class)
            )
        );
    }

    public function testInvokation()
    {
        $render = new Logout(
            $manager = $this->createMock(Manager::class),
            $generator = $this->createMock(UrlGenerator::class),
            $clock = $this->createMock(TimeContinuumInterface::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $manager
            ->expects($this->once())
            ->method('get')
            ->with($request)
            ->willReturn(new Session(
                new Id('sid'),
                new Name('sname'),
                new Map('string', 'mixed')
            ));
        $manager
            ->expects($this->once())
            ->method('close')
            ->with($request);
        $generator
            ->expects($this->once())
            ->method('__invoke')
            ->with(new Route\Name('index'))
            ->willReturn(Url::fromString('/'));
        $clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(new PointInTime('2018-08-15T12:13:14+0200'));

        $response = $render(
            $request,
            Route::of(new Route\Name('logout'), Str::of('GET /logout')),
            new Map('string', 'string')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->statusCode()->value());
        $this->assertSame(
            'Location : /',
            (string) $response->headers()->get('Location')
        );
        $this->assertSame(
            'Set-Cookie : sname=sid; Expires="Sun, 15 Jul 2018 12:13:14 +0200"',
            (string) $response->headers()->get('Set-Cookie')
        );
    }
}
