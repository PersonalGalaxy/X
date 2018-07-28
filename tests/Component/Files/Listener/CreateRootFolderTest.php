<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Listener;

use PersonalGalaxy\X\Component\Files\{
    Listener\CreateRootFolder,
    Entity\Folder,
};
use PersonalGalaxy\Identity\{
    Entity\Identity\Identity,
    Entity\Identity\Email,
    Event\IdentityWasCreated,
};
use Innmind\Neo4j\ONM\{
    Manager,
    Identity\Generators,
    Identity\Generator,
};
use Innmind\Neo4j\DBAL\Connection;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CreateRootFolderTest extends TestCase
{
    public function testInvokation()
    {
        $create = new CreateRootFolder(
            $dbal = $this->createMock(Connection::class),
            $manager = $this->createMock(Manager::class)
        );
        $manager
            ->expects($this->once())
            ->method('identities')
            ->willReturn(new Generators(
                (new Map('string', Generator::class))
                    ->put(Folder::class, $generator = $this->createMock(Generator::class))
            ));
        $generator
            ->expects($this->once())
            ->method('new')
            ->willReturn(new Folder('341bcfca-ff69-4d8b-b90b-74e4ad4008ad'));
        $dbal
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(static function($query): bool {
                return (string) $query === 'MATCH (user:User { identity: {user} }) CREATE (user)<-[:OWNED_BY]-(root:Files:Folder:Root { identity: {root} })' &&
                    $query->parameters()->get('user')->value() === 'user uuid' &&
                    $query->parameters()->get('root')->value() === '341bcfca-ff69-4d8b-b90b-74e4ad4008ad';
            }));
        $identity = $this->createMock(Identity::class);
        $identity
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('user uuid');

        $this->assertNull($create(new IdentityWasCreated(
            $identity,
            new Email('foo@bar.baz')
        )));
    }
}
