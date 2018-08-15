<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Web\Authentication\Fallback;

use Innmind\HttpFramework\Authenticate\Fallback;
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\StatusCode\StatusCode,
    Headers\Headers,
    Header\ContentType,
    Header\ContentTypeValue,
    Header\WWWAuthenticate,
    Header\WWWAuthenticateValue,
};
use Innmind\Url\Authority\NullUserInformation;
use Innmind\Templating\{
    Engine,
    Name,
};

final class Login implements Fallback
{
    private $render;

    public function __construct(Engine $render)
    {
        $this->render = $render;
    }

    public function __invoke(ServerRequest $request, \Exception $e): Response
    {
        return new Response\Response(
            $code = StatusCode::of('UNAUTHORIZED'),
            $code->associatedReasonPhrase(),
            $request->protocolVersion(),
            Headers::of(
                new ContentType(
                    new ContentTypeValue('text', 'html')
                ),
                new WWWAuthenticate(
                    new WWWAuthenticateValue(
                        'Bearer',
                        (string) $request
                            ->url()
                            ->authority()
                            ->withUserInformation(new NullUserInformation)
                    )
                )
            ),
            ($this->render)(new Name('login.html.twig'))
        );
    }
}
