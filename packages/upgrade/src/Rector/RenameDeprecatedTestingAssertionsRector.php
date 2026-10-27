<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Livewire\Features\SupportTesting\Testable;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
    /**
     * @var array<string, array{string, class-string}>
     */
    private const ASSERTIONS = [
        'assertFormFieldIsDisabled' => ['assertFormFieldDisabled', TestsForms::class],
        'assertFormFieldIsEnabled' => ['assertFormFieldEnabled', TestsForms::class],
        'assertFormFieldIsReadOnly' => ['assertFormFieldReadOnly', TestsForms::class],
        'assertFormFieldIsHidden' => ['assertFormFieldHidden', TestsForms::class],
        'assertFormFieldIsVisible' => ['assertFormFieldVisible', TestsForms::class],
        'assertActionHeld' => ['assertActionHalted', TestsActions::class],
        'assertTableActionHeld' => ['assertTableActionHalted', TestsTableActions::class],
        'assertTableActionsExistInOrder' => ['assertTableRecordActionsExistInOrder', TestsTableActions::class],
        'assertTableBulkActionHeld' => ['assertTableBulkActionHalted', TestsBulkActions::class],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename deprecated Filament testing macros on known Livewire test receivers', [
            new CodeSample(
                '$testable->assertFormFieldIsDisabled(key: "title");',
                '$testable->assertFormFieldDisabled(key: "title");',
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
        // Macro keys are case-sensitive, unlike ordinary PHP methods.
        if ((! $node->name instanceof Identifier) || (! isset(self::ASSERTIONS[$node->name->name]))) {
            return null;
        }

        [$replacement, $mixin] = self::ASSERTIONS[$node->name->name];

        if ((! $this->isTestable($node->var)) && (! $this->isExactType($node->var, $mixin))) {
            return null;
        }

        // Custom registrations can replace either macro. Native unions may
        // normalize an owning class and its overriding subclass to the owner.
        if ((new NodeFinder)->findFirst($this->getFile()->getNewStmts(), function (Node $candidate): bool {
            if (($candidate instanceof StaticCall) || ($candidate instanceof MethodCall) || ($candidate instanceof NullsafeMethodCall)) {
                return $candidate->name instanceof Identifier && in_array(strtolower($candidate->name->name), ['macro', 'mixin', '__call', '__callstatic', 'macrocall'], strict: true);
            }

            if (! $candidate instanceof UnionType) {
                return false;
            }

            foreach ($candidate->types as $type) {
                foreach ([Testable::class, TestsForms::class, TestsActions::class, TestsTableActions::class, TestsBulkActions::class] as $owner) {
                    if ((new ObjectType($owner))->isSuperTypeOf($this->getType($type))->yes()) {
                        return true;
                    }
                }
            }

            return false;
        })) {
            return null;
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function isTestable(Expr $receiver): bool
    {
        if (($receiver instanceof MethodCall) || ($receiver instanceof NullsafeMethodCall)) {
            if ((! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
                return false;
            }

            if ($this->isTestable($receiver->var)) {
                $method = $receiver->name->name;

                // Do not trust `__call()`'s `$this` PHPDoc for arbitrary macros,
                // which can return other objects instead of the test receiver.
                if (isset(self::ASSERTIONS[$method]) || in_array($method, array_column(self::ASSERTIONS, 0), strict: true)) {
                    return true;
                }

                if (in_array($method, ['fillForm', 'mountAction', 'callAction', 'mountTableAction', 'callTableAction', 'mountTableBulkAction', 'callTableBulkAction', 'assertDispatched', 'assertNotDispatched', 'assertDispatchedTo'], strict: true)) {
                    return true;
                }

                $reflection = (new ObjectType(Testable::class))->getClassReflection();

                if (! $reflection?->hasNativeMethod($method)) {
                    return false;
                }

                return $reflection->getNativeMethod($method)->getVariants()[0]->getReturnType() instanceof StaticType;
            }
        }

        return $this->isExactType($receiver, Testable::class);
    }

    /**
     * @param  class-string  $class
     */
    private function isExactType(Expr $receiver, string $class): bool
    {
        // Custom subclasses may override native methods or magic dispatch.
        $type = TypeCombinator::removeNull(ScopeFetcher::fetch($receiver)->getType($receiver));

        return $type->isObject()->yes() && ($type->getObjectClassNames() === [$class]);
    }
}
