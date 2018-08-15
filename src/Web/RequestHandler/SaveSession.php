<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\RequestHandler;

use Innmind\HttpFramework\RequestHandler;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\HttpSession\Manager;

final class SaveSession implements RequestHandler
{
    private $handle;
    private $manager;

    public function __construct(
        RequestHandler $handle,
        Manager $manager
    ) {
        $this->handle = $handle;
        $this->manager = $manager;
    }

    public function __invoke(ServerRequest $request): Response
    {
        $response = ($this->handle)($request);

        if ($this->manager->has($request)) {
            $this->manager->save($request);
        }

        return $response;
    }
}
