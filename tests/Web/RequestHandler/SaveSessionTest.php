<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\RequestHandler;

use PersonalGalaxy\X\Web\RequestHandler\SaveSession;
use Innmind\HttpFramework\RequestHandler;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\HttpSession\Manager;
use PHPUnit\Framework\TestCase;

class SaveSessionTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            RequestHandler::class,
            new SaveSession(
                $this->createMock(RequestHandler::class),
                $this->createMock(Manager::class)
            )
        );
    }

    public function testDoNothingWhenSessionNotStarted()
    {
        $save = new SaveSession(
            $handler = $this->createMock(RequestHandler::class),
            $manager = $this->createMock(Manager::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($response = $this->createMock(Response::class));
        $manager
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(false);
        $manager
            ->expects($this->never())
            ->method('save');

        $this->assertSame($response, $save($request));
    }

    public function testSaveWhenSessionStarted()
    {
        $save = new SaveSession(
            $handler = $this->createMock(RequestHandler::class),
            $manager = $this->createMock(Manager::class)
        );
        $request = $this->createMock(ServerRequest::class);
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($response = $this->createMock(Response::class));
        $manager
            ->expects($this->once())
            ->method('has')
            ->with($request)
            ->willReturn(true);
        $manager
            ->expects($this->once())
            ->method('save')
            ->with($request);

        $this->assertSame($response, $save($request));
    }
}
