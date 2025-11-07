<?php

declare(strict_types=1);

namespace Utils\PHPStan;

use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionClass;

final class JsonResourceStaticMethodsRule implements Rule
{
    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $notAllowedMethods = [
            'withoutWrapping',
            'wrap',
        ];

        if (! $node->class instanceof FullyQualified) {
            return [];
        }

        $reflector = new ReflectionClass($node->class->toString());

        if (!$reflector->isSubclassOf(JsonResource::class)) {
            return [];
        }

        $methodName = $node->name->toString();

        if (in_array($methodName, $notAllowedMethods)) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s static calls to JsonResource are not allowed. Modification of static properties from outside the class is prohibited',
                    $methodName
                ))
                    ->identifier(sprintf('customRules.%s', $methodName))
                    ->build(),
            ];
        }

        return [];
    }
}
