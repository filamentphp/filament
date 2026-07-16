<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Resources\Resource;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\UnionType;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

class SimpleMethodChangesRector extends AbstractRector
{
    /**
     * @return array<array{
     *     class: class-string | array<class-string>,
     *     changes: array<string, Closure>,
     * }>
     */
    public function getChanges(): array
    {
        $addUnitEnumToAuthorizationActionParamModifier = static function (ClassMethod $node): void {
            foreach ($node->getParams() as $param) {
                if ($param->var->name !== 'action') {
                    continue;
                }

                $param->type = new UnionType([new Identifier('string'), new FullyQualified('UnitEnum')]);
            }
        };

        return [
            [
                'class' => [
                    Resource::class,
                ],
                'changes' => [
                    'getAuthorizationResponse' => $addUnitEnumToAuthorizationActionParamModifier,
                    'can' => $addUnitEnumToAuthorizationActionParamModifier,
                    'authorize' => $addUnitEnumToAuthorizationActionParamModifier,
                ],
            ],
        ];
    }

    public function getNodeTypes(): array
    {
        return [Class_::class, Enum_::class];
    }

    /**
     * @param  Class_ | Enum_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $touched = false;

        foreach ($this->getChanges() as $change) {
            if (! $this->isClassMatchingChange($node, $change)) {
                continue;
            }

            foreach ($change['changes'] as $methodName => $modifier) {
                foreach ($node->getMethods() as $method) {
                    if (! $this->isName($method, $methodName)) {
                        continue;
                    }

                    $modifier($method);

                    $touched = true;
                }
            }
        }

        return $touched ? $node : null;
    }

    /**
     * @param array{
     *     class: class-string | array<class-string>,
     * } $change
     */
    public function isClassMatchingChange(Class_ | Enum_ $class, array $change): bool
    {
        $classes = is_array($change['class']) ?
            $change['class'] :
            [$change['class']];

        $classes = array_map(fn (string $class): string => ltrim($class, '\\'), $classes);

        foreach ($classes as $classToCheck) {
            if ($class instanceof Enum_) {
                foreach ($class->implements as $enumInterface) {
                    if ($enumInterface->toString() === $classToCheck) {
                        return true;
                    }
                }

                continue;
            }

            if ($this->isObjectType($class, new ObjectType($classToCheck))) {
                return true;
            }
        }

        return false;
    }
}
