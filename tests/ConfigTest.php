<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

final class ConfigTest extends TestCase
{
    public function testDi(): void
    {
        $definitions = array_merge(
            require dirname(__DIR__) . '/config/di.php',
            [
                HydratorInterface::class => Hydrator::class,
                ValidatorInterface::class => Validator::class,
            ],
        );

        $container = new Container(
            ContainerConfig::create()
                ->withDefinitions($definitions)
        );

        $formHydrator = $container->get(FormHydrator::class);

        $this->assertInstanceOf(FormHydrator::class, $formHydrator);
    }
}
