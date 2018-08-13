<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X;

use Innmind\Compose\ContainerBuilder\ContainerBuilder;
use Innmind\Url\Path;
use Innmind\CLI\Commands;
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Immutable\Map;
use Symfony\Component\Yaml\Yaml;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testBuildApp()
    {
        $container = (new ContainerBuilder)(
            new Path(__DIR__.'/../config/container/app.yml'),
            (new Map('string', 'mixed'))
                ->put('metas', [Yaml::parseFile(__DIR__.'/../config/neo4j/entities.yml')])
                ->put('neo4jPassword', 'ci')
                ->put('filesStoragePath', '/tmp/personal-galaxy')
        );

        $this->assertInstanceOf(Commands::class, $container->get('commands'));
        $this->assertInstanceOf(CommandBusInterface::class, $container->get('commandBus'));
    }
}
