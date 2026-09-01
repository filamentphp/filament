<?php

namespace Filament\Upgrade\Rector;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
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

class ConvertStaticConfigurationToConfigureUsingFunction extends AbstractRector
{
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
                if (! $this->isObjectType($node->expr->var->class, new ObjectType($class))) {
                    continue;
                }

                foreach ($change['properties'] as $property) {
                    if (! $this->isName($node->expr->var->name, $property)) {
                        continue;
                    }

                    $node->expr = new StaticCall(
                        class: $node->expr->var->class,
                        name: new Identifier('configureUsing'),
                        args: [
                            new Arg(
                                value: new ArrowFunction(
                                    subNodes: [
                                        'params' => [
                                            new Param(
                                                var: new Variable(lcfirst(class_basename($node->expr->var->class->toCodeString()))),
                                                type: $node->expr->var->class,
                                            ),
                                        ],
                                        'expr' => new MethodCall(
                                            var: new Variable(lcfirst(class_basename($node->expr->var->class->toCodeString()))),
                                            name: new Identifier($node->expr->var->name->toString()),
                                            args: [
                                                new Arg(
                                                    value: $node->expr->expr,
                                                ),
                                            ],
                                        ),
                                    ],
                                ),
                            ),
                        ],
                    );

                    return $node;
                }
            }
        }
    }
}
