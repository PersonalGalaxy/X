<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Controller;

use PersonalGalaxy\X\Web\Controller\Index;
use Innmind\HttpFramework\Controller;
use Innmind\Http\Message\Response;
use Innmind\Templating\Engine;
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
}
