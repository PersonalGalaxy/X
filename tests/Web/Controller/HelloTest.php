<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Controller;

use PersonalGalaxy\X\Web\Controller\Hello;
use Innmind\HttpFramework\Controller;
use Innmind\Http\Message\Response;
use Innmind\Templating\Engine;
use Tests\PersonalGalaxy\X\Web\TestCase;

class HelloTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Controller::class,
            new Hello(
                $this->createMock(Engine::class)
            )
        );
    }

    public function testIndex()
    {
        $response = $this->request('get', '/');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->statusCode()->value());
        $this->assertSame("Hello world!\n", (string) $response->body());
    }
}
