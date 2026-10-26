<?php

namespace Filament\Upgrade\Rector;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedSchemaMethodsRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const METHOD_RENAMES = [
        Schema::class => [
            'name' => 'key',
            'getName' => 'getKey',
            'context' => 'operation',
            'clearCachedDefaultChildSchemas' => 'clearCachedChildSchemas',
        ],
        Component::class => [
            'getGetCallback' => 'makeGetUtility',
            'getSetCallback' => 'makeSetUtility',
            'getChildComponentContainer' => 'getChildSchema',
            'getChildComponentContainers' => 'getChildSchemas',
            'clearCachedDefaultChildSchemas' => 'clearCachedChildSchemas',
        ],
        Step::class => [
            'afterValidated' => 'afterValidation',
        ],
        ViewComponent::class => [
            'disableLabel' => 'hiddenLabel',
        ],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament schema forwarding method calls', [
            new CodeSample(
                '$schema->name(name: "details")->context(context: "edit");',
                '$schema->key(key: "details")->operation(operation: "edit");',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = strtolower($node->name->toString());
        $receiverType = $this->resolveReceiverType($node->var);

        // `getName()` ignored extra positional arguments; `getKey()` would consume one.
        if (($methodName === 'getname') && ($node->isFirstClassCallable() || count($node->getArgs()))) {
            return null;
        }

        foreach (self::METHOD_RENAMES as $componentClass => $methodRenames) {
            $methodRenames = array_change_key_case($methodRenames);

            if (! isset($methodRenames[$methodName])) {
                continue;
            }

            if (! (new ObjectType($componentClass))->isSuperTypeOf($receiverType)->yes()) {
                continue;
            }

            // Custom overrides need manual migration to preserve parameters and dispatch.
            foreach ($receiverType->getObjectClassReflections() as $classReflection) {
                foreach ([$methodName, $methodRenames[$methodName]] as $candidateMethodName) {
                    if (! $classReflection->hasNativeMethod($candidateMethodName)) {
                        if ($candidateMethodName === $methodName) {
                            continue;
                        }

                        return null;
                    }

                    $methodReflection = $classReflection->getNativeMethod($candidateMethodName);

                    if (! $methodReflection->isPublic()) {
                        return null;
                    }

                    if ($methodName === 'disablelabel') {
                        // This concern also belongs to fields, entries, stats, and editor tools.
                        if (
                            (! method_exists($methodReflection, 'getDeclaringTrait')) ||
                            ($methodReflection->getDeclaringTrait()?->getName() !== HasLabel::class)
                        ) {
                            return null;
                        }
                    } elseif (
                        ($methodReflection->getDeclaringClass()->getName() !== $componentClass) &&
                        // The built-in relationship concern preserves this getter's contract.
                        (! (
                            ($candidateMethodName === 'getChildSchema') &&
                            method_exists($methodReflection, 'getDeclaringTrait') &&
                            ($methodReflection->getDeclaringTrait()?->getName() === EntanglesStateWithSingularRelationship::class)
                        ))
                    ) {
                        return null;
                    }
                }
            }

            $renamedArgument = match ($methodName) {
                'name' => ['name', 'key'],
                'context' => ['context', 'operation'],
                default => null,
            };

            if ($renamedArgument && (! $node->isFirstClassCallable())) {
                foreach ($node->getArgs() as $argument) {
                    if ($argument->unpack) {
                        return null;
                    }
                }

                foreach ($node->getArgs() as $argument) {
                    if ($argument->name?->toString() === $renamedArgument[0]) {
                        $argument->name = new Identifier($renamedArgument[1]);
                    }
                }
            }

            $node->name = new Identifier($methodRenames[$methodName]);

            return $node;
        }

        return null;
    }

    private function resolveReceiverType(Expr $receiver): Type
    {
        $receiverType = TypeCombinator::removeNull($this->getType($receiver));

        if (
            ((! $receiver instanceof MethodCall) && (! $receiver instanceof NullsafeMethodCall)) ||
            (! $receiver->name instanceof Identifier) ||
            $receiver->isFirstClassCallable()
        ) {
            return $receiverType;
        }

        $callerType = $this->resolveReceiverType($receiver->var);

        if ($callerType->isSuperTypeOf($receiverType)->yes()) {
            return $receiverType;
        }

        $classReflections = $callerType->getObjectClassReflections();

        if (! count($classReflections)) {
            return $receiverType;
        }

        foreach ($classReflections as $classReflection) {
            if (! $classReflection->hasNativeMethod($receiver->name->toString())) {
                return $receiverType;
            }

            $methodReflection = $classReflection->getNativeMethod($receiver->name->toString());

            if (! $methodReflection->getVariants()[0]->getReturnType() instanceof StaticType) {
                return $receiverType;
            }
        }

        // Inherited `static` returns retain the caller's class, not the declaring class.
        return $callerType;
    }
}
