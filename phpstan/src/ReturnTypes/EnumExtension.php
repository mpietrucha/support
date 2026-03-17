<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\ReturnTypes;

use Illuminate\Support\Arr;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * @internal
 */
final class EnumExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return EnumInterface::class;
    }

    public function isMethodSupported(MethodReflection $method): bool
    {
        return $method->getName() === 'value';
    }

    public function getTypeFromMethodCall(MethodReflection $method, MethodCall $call, Scope $scope): Type
    {
        $reflection = $scope->getType(
            $call->var
        )->getObjectClassReflections() |> Arr::first(...);

        $fallback = TypeCombinator::union(new StringType, new IntegerType);

        if ($reflection === null) {
            return $fallback;
        }

        if (! $reflection->isEnum()) {
            return $fallback;
        }

        if (! $reflection->isBackedEnum()) {
            return new StringType;
        }

        /** @var Type $type */
        $type = $reflection->getBackedEnumType();

        return match (true) {
            $type->isInteger()->yes() => new IntegerType,
            default => new StringType
        };
    }
}
