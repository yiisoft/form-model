<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/config', isDev: false)
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // Non-namespaced test fixture, manually `require`d (does not fit PSR-4 autoloading).
    ->ignoreUnknownClasses(['NonNamespacedForm'])
    // `yiisoft/definitions` is used only in `config/di.php`, which is loaded by consumers using
    // `yiisoft/di`, that already requires `yiisoft/definitions` itself.
    ->ignoreErrorsOnPackages(['yiisoft/definitions'], [ErrorType::SHADOW_DEPENDENCY]);
