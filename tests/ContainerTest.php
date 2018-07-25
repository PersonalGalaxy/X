<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X;

use Innmind\Compose\ContainerBuilder\ContainerBuilder;
use Innmind\Url\Path;
use Innmind\CLI\Commands;
use Innmind\Immutable\Map;
use Symfony\Component\Yaml\Yaml;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testBuild()
    {
        $container = (new ContainerBuilder)(
            new Path(__DIR__.'/../config/container.yml'),
            (new Map('string', 'mixed'))
                ->put('metas', [Yaml::parseFile(__DIR__.'/../config/neo4j/entities.yml')])
                ->put('neo4jPassword', 'ci')
                ->put('filesStoragePath', '/tmp/personal-galaxy')
        );

        $this->assertInstanceOf(Commands::class, $container->get('commands'));
    }
}
