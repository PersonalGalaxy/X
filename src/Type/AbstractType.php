<?php
declare(strict_types = 1);

namespace PersonalGalaxy\X\Type;

use Innmind\Reflection\{
    ReflectionClass,
    Instanciator\ConstructorLessInstanciator,
};

abstract class AbstractType
{
    protected $reflection;

    protected function __construct(string $class)
    {
        $this->reflection = new ReflectionClass(
            $class,
            null,
            null,
            new ConstructorLessInstanciator
        );
    }
}
