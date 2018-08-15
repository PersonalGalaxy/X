<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Controller;

use PersonalGalaxy\X\Web\Controller\Index;
use Innmind\HttpFramework\Controller;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\Templating\{
    Engine,
    Name,
};
use Innmind\Stream\Readable;
use Innmind\Router\Route;
use Innmind\Immutable\{
    Map,
    Str,
};
use Tests\PersonalGalaxy\X\Web\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IndexTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Controller::class,
            new Index(
                $this->createMock(Engine::class)
            )
        );
    }

    public function testIndex()
    {
        $response = $this->request('get', '/');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(401, $response->statusCode()->value());
        $this->assertNotEmpty((string) $response->body());
    }

    public function testInvokation()
    {
        $render = new Index(
            $templating = $this->createMock(Engine::class)
        );
        $templating
            ->expects($this->once())
            ->method('__invoke')
            ->with(new Name('index.html.twig'))
            ->willReturn($stream = $this->createMock(Readable::class));
        $request = $this->createMock(ServerRequest::class);

        $response = $render(
            $request,
            Route::of(new Route\Name('index'), Str::of('GET /')),
            new Map('string', 'string')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->statusCode()->value());
        $this->assertSame($stream, $response->body());
    }
}
