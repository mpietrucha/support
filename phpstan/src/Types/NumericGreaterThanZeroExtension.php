<?php

namespace Mpietrucha\PHPStan\Types;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;

final class NumericGreaterThanZeroExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    /**
     * @param  class-string  $class
     */
    public function __construct(private string $class)
    {
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function isStaticMethodSupported(MethodReflection $reflection, StaticCall $call, TypeSpecifierContext $context): bool
    {
        if (! $context->true()) {
            return false;
        }

        return $reflection->getName() === 'numericGreaterThanZero';
    }

    public function specifyTypes(MethodReflection $reflection, StaticCall $call, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $numeric = TypeCombinator::union(
            new FloatType,
            new IntegerType,
            new IntersectionType([
                new StringType, new AccessoryNumericStringType,
            ])
        );

        $arguments = $call->getArgs() |> collect(...);

        return $arguments->reduce(function (SpecifiedTypes $specifiedTypes, $arg) use ($numeric, $context, $scope, $call): SpecifiedTypes {
            if ($arg->unpack) {
                return $specifiedTypes;
            }

            $type = $this->typeSpecifier->create(
                $arg->value,
                $numeric,
                $context,
                $scope
            )->setRootExpr($call);

            return $specifiedTypes->unionWith($type);
        }, new SpecifiedTypes);
    }
}
