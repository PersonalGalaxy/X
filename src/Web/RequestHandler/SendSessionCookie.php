<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\RequestHandler;

use PersonalGalaxy\X\Web\Authentication\Identity\Fresh;
use Innmind\HttpFramework\RequestHandler;
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\StatusCode\StatusCode,
    Headers\Headers,
    Header\Location,
    Header\LocationValue,
    Header\SetCookie,
    Header\CookieValue,
    Header\CookieParameter\HttpOnly,
    Header\CookieParameter\SameSite,
    Header\CookieParameter\Secure,
    Header\CookieParameter\Expires,
    Header\Parameter\Parameter,
};
use Innmind\HttpAuthentication\ViaStorage\Storage;
use Innmind\HttpSession\Manager;
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    Move\Earth\Month,
};

final class SendSessionCookie implements RequestHandler
{
    private $handle;
    private $storage;
    private $manager;
    private $clock;

    public function __construct(
        RequestHandler $handle,
        Storage $storage,
        Manager $manager,
        TimeContinuumInterface $clock
    ) {
        $this->handle = $handle;
        $this->storage = $storage;
        $this->manager = $manager;
        $this->clock = $clock;
    }

    public function __invoke(ServerRequest $request): Response
    {
        if (!$this->storage->has($request)) {
            return ($this->handle)($request);
        }

        $identity = $this->storage->get($request);

        if ($identity instanceof Fresh) {
            $session = $this->manager->get($request);

            return new Response\Response(
                $code = StatusCode::of('FOUND'),
                $code->associatedReasonPhrase(),
                $request->protocolVersion(),
                Headers::of(
                    new Location(
                        new LocationValue($request->url())
                    ),
                    new SetCookie(
                        new CookieValue(
                            new Parameter(
                                (string) $session->name(),
                                (string) $session->id()
                            ),
                            new HttpOnly,
                            SameSite::strict(),
                            new Secure,
                            new Expires(
                                Month::forward(3)(
                                    $this->clock->now()
                                )
                            )
                            // Domain not sent to restrict to the current
                            // domain only and not subdomains
                        )
                    )
                )
            );
        }

        return ($this->handle)($request);
    }
}
