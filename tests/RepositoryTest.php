<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X;

use PersonalGalaxy\X\Repository;
use Innmind\Neo4j\ONM\{
    Manager,
    Repository as RepositoryInterface,
};
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testBuild()
    {
        $repositoryStruct = new class($this->createMock(RepositoryInterface::class)) {
            public function __construct(RepositoryInterface $watev) {
            }
        };
        $manager = $this->createMock(Manager::class);
        $manager
            ->expects($this->once())
            ->method('repository')
            ->with('foo')
            ->willReturn($this->createMock(RepositoryInterface::class));

        $repository = Repository::build(
            $manager,
            get_class($repositoryStruct),
            'foo'
        );

        $this->assertInstanceOf(get_class($repositoryStruct), $repository);
    }
}
