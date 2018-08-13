<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Controller;

use Innmind\HttpFramework\Controller;
use Innmind\Router\Route;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
    StatusCode\StatusCode,
};
use Innmind\Templating\{
    Engine,
    Name,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class Hello implements Controller
{
    private $render;

    public function __construct(Engine $engine)
    {
        $this->render = $engine;
    }

    public function __invoke(
        ServerRequest $request,
        Route $route,
        MapInterface $arguments
    ): Response {
        return new Response\Response(
            $code = StatusCode::of('OK'),
            $code->associatedReasonPhrase(),
            $request->protocolVersion(),
            null,
            ($this->render)(
                new Name('hello.html.twig'),
                (new Map('string', 'mixed'))
                    ->put('name', 'world')
            )
        );
    }
}
