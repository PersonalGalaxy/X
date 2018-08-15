<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Authentication;

use PersonalGalaxy\X\Web\Authentication\Identity\Fresh;
use Innmind\HttpAuthentication\{
    Authenticator,
    Identity,
};
use Innmind\Http\Message\ServerRequest;
use Innmind\HttpSession\Manager;

final class ViaSession implements Authenticator
{
    private $authenticate;
    private $manager;

    public function __construct(
        Authenticator $authenticate,
        Manager $manager
    ) {
        $this->authenticate = $authenticate;
        $this->manager = $manager;
    }

    public function __invoke(ServerRequest $request): Identity
    {
        if (!$this->manager->has($request)) {
            $this->manager->start($request);
        }

        $session = $this->manager->get($request);

        if ($session->has('identity')) {
            return $session->get('identity');
        }

        try {
            $identity = ($this->authenticate)($request);
        } catch (\Throwable $e) {
            $this->manager->close($request);

            throw $e;
        }

        $session->set('identity', $identity);

        return new Fresh($identity);
    }
}
