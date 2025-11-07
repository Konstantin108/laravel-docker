<?php

declare(strict_types=1);

namespace Utils\PHPStan;

use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionClass;

final class JsonResourceStaticPropertyRule implements Rule
{
    public function getNodeType(): string
    {
        return Assign::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof StaticPropertyFetch) {
            return [];
        }

        $reflector = new ReflectionClass($node->var->class->toString());

        if (! $reflector->isSubclassOf(JsonResource::class)) {
            return [];
        }

        $varName = $node->var->name;

        if ((string) $varName !== 'wrap') {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf('Assigning static $%s property outside class is prohibited', $varName))
                ->identifier('customRules.propertyAssignment')
                ->build(),
        ];
    }
}
