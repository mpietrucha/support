<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPaths([
        'src',
        'analyze',
    ])
    ->withSkip([
        RemoveDeadStmtRector::class,
        ClosureToArrowFunctionRector::class,
        RemoveNonExistingVarAnnotationRector::class,
    ])
    ->withRules([
        StaticClosureRector::class,
        StaticArrowFunctionRector::class,
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true
    )
    ->withPhpSets(php85: true)
    ->withBootstrapFiles([
        'vendor/larastan/larastan/bootstrap.php',
    ])
    ->withPhpstanConfigs([
        'phpstan.neon',
    ]);
