<?php

namespace Filament\Upgrade\Rector;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\VarLikeIdentifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ClassReflectionAnalyzer;

class ConvertStaticConfigurationToConfigureUsingFunction extends AbstractRector
{
    public function __construct(
        private ClassReflectionAnalyzer $classReflectionAnalyzer,
    ) {}

    /**
     * @return array<array{
     *     class: class-string | array<class-string>,
     *     properties: array<string>,
     * }>
     */
    public function getChanges(): array
    {
        return [
            [
                'class' => [
                    DateTimePicker::class,
                    Schema::class,
                    Table::class,
                ],
                'properties' => [
                    'defaultDateDisplayFormat',
                    'defaultDateTimeDisplayFormat',
                    'defaultTimeDisplayFormat',
                ],
            ],
            [
                'class' => [
                    DateTimePicker::class,
                ],
                'properties' => [
                    'defaultDateTimeWithSecondsDisplayFormat',
                    'defaultTimeWithSecondsDisplayFormat',
                ],
            ],
            [
                'class' => [
                    Schema::class,
                    Table::class,
                ],
                'properties' => [
                    'defaultCurrency',
                    'defaultIsoDateDisplayFormat',
                    'defaultIsoDateTimeDisplayFormat',
                    'defaultIsoTimeDisplayFormat',
                    'defaultNumberLocale',
                ],
            ],
        ];
    }

    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    public function refactor(Node $node): ?Expression
    {
        if (! ($node instanceof Expression)) {
            return null;
        }

        if (! ($node->expr instanceof Assign)) {
            return null;
        }

        if (! ($node->expr->var instanceof StaticPropertyFetch)) {
            return null;
        }

        if (! ($node->expr->var->class instanceof FullyQualified)) {
            return null;
        }

        if (! ($node->expr->var->name instanceof VarLikeIdentifier)) {
            return null;
        }

        foreach ($this->getChanges() as $change) {
            foreach ((is_array($change['class']) ? $change['class'] : [$change['class']]) as $class) {
                if (! $this->isObjectTypeIncludingRenamedParents($node->expr->var->class, new ObjectType($class))) {
                    continue;
                }

                foreach ($change['properties'] as $property) {
                    if (! $this->isName($node->expr->var->name, $property)) {
                        continue;
                    }

                    // Evaluate the RHS in its original scope, then capture its value without introducing a local variable.
                    $node->expr = new StaticCall(
                        class: $node->expr->var->class,
                        name: new Identifier('configureUsing'),
                        args: [
                            new Arg(
                                value: new FuncCall(
                                    name: new ArrowFunction(
                                        subNodes: [
                                            'static' => true,
                                            'params' => [new Param(var: new Variable('value'))],
                                            'expr' => new ArrowFunction(
                                                subNodes: [
                                                    'static' => true,
                                                    'params' => [
                                                        new Param(
                                                            var: new Variable('component'),
                                                            type: new FullyQualified($node->expr->var->class->toString()),
                                                        ),
                                                    ],
                                                    'expr' => new MethodCall(
                                                        var: new Variable('component'),
                                                        name: new Identifier($node->expr->var->name->toString()),
                                                        args: [new Arg(value: new Variable('value'))],
                                                    ),
                                                ],
                                            ),
                                        ],
                                    ),
                                    args: [
                                        new Arg(value: $node->expr->expr),
                                    ],
                                ),
                            ),
                        ],
                    );

                    return $node;
                }
            }
        }

        return null;
    }

    private function isObjectTypeIncludingRenamedParents(FullyQualified $name, ObjectType $requiredType): bool
    {
        if ($this->isObjectType($name, $requiredType)) {
            return true;
        }

        $type = $this->getType($name);

        if (! ($type instanceof ObjectType)) {
            return false;
        }

        // PHPStan may omit a removed v3 parent from the analysed ancestry before Rector's renames are applied.
        foreach ($type->getClassReflection()?->getAncestors() ?? [] as $ancestor) {
            $parentClassName = $this->classReflectionAnalyzer->resolveParentClassName($ancestor);

            if (($parentClassName !== null) && $this->isObjectType(new FullyQualified($parentClassName), $requiredType)) {
                return true;
            }
        }

        return false;
    }
}
