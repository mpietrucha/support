<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\ReturnTypes;

use Illuminate\Support\Arr;
use Mpietrucha\Support\Enum;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericClassStringType;
use PHPStan\Type\Type;

/**
 * @internal
 */
final class EnumExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Enum::class;
    }

    public function isStaticMethodSupported(MethodReflection $method): bool
    {
        return $method->getName() === 'get';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $method, StaticCall $call, Scope $scope): Type
    {
        /** @var ParametersAcceptor $definition */
        $definition = $method->getVariants() |> Arr::first(...);

        $enum = $call->getArgs() |> Arr::first(...);

        if ($enum === null) {
            return $definition->getReturnType();
        }

        $type = $enum->value |> $scope->getType(...);

        if (! $type->isClassString()->yes()) {
            return $definition->getReturnType();
        }

        return new GenericClassStringType($type->getClassStringObjectType());
    }
}
