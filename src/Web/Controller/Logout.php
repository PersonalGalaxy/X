<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Controller;

use Innmind\HttpFramework\Controller;
use Innmind\HttpSession\Manager;
use Innmind\Router\{
    UrlGenerator,
    Route,
    Route\Name,
};
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\StatusCode\StatusCode,
    Headers\Headers,
    Header\SetCookie,
    Header\CookieValue,
    Header\CookieParameter\Expires,
    Header\Parameter\Parameter,
    Header\Location,
    Header\LocationValue,
};
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    Move\Earth\Month,
};
use Innmind\Immutable\MapInterface;

final class Logout implements Controller
{
    private $manager;
    private $generate;
    private $clock;

    public function __construct(
        Manager $manager,
        UrlGenerator $generate,
        TimeContinuumInterface $clock
    ) {
        $this->manager = $manager;
        $this->generate = $generate;
        $this->clock = $clock;
    }

    public function __invoke(
        ServerRequest $request,
        Route $route,
        MapInterface $arguments
    ): Response {
        $session = $this->manager->get($request);
        $this->manager->close($request);

        return new Response\Response(
            $code = StatusCode::of('FOUND'),
            $code->associatedReasonPhrase(),
            $request->protocolVersion(),
            Headers::of(
                new SetCookie(
                    new CookieValue(
                        new Parameter(
                            (string) $session->name(),
                            (string) $session->id()
                        ),
                        new Expires(
                            Month::backward(1)(
                                $this->clock->now()
                            )
                        )
                    )
                ),
                new Location(
                    new LocationValue(
                        ($this->generate)(new Name('index'))
                    )
                )
            )
        );
    }
}
