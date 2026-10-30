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
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class RenameDeprecatedTestingAssertionsRector extends AbstractRector
{
    /** @var array<string, array{string, class-string}> */
    private const ASSERTIONS = [
        'setActionData' => ['fillForm', Testable::class],
        'assertActionDataSet' => ['assertSchemaStateSet', Testable::class],
        'assertHasActionErrors' => ['assertHasFormErrors', Testable::class],
        'assertHasNoActionErrors' => ['assertHasNoFormErrors', Testable::class],
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

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || (! isset(self::ASSERTIONS[$node->name->name]))) {
            return null;
        }

        [$replacement, $mixin] = self::ASSERTIONS[$node->name->name];

        if ((! $this->isTestable($node->var)) && (! $this->isObjectType($node->var, new ObjectType($mixin)))) {
            return null;
        }

        if (in_array($node->name->name, ['setActionData', 'assertActionDataSet'], strict: true) && (! $node->isFirstClassCallable())) {
            foreach ($node->args as $argument) {
                if ($argument->name?->name === 'data') {
                    $argument->name = new Identifier('state');
                }
            }
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

                return isset(self::ASSERTIONS[$method])
                    || in_array($method, array_column(self::ASSERTIONS, 0), strict: true)
                    || in_array($method, ['set', 'call', 'fillForm', 'mountAction', 'callAction', 'mountTableAction', 'callTableAction', 'mountTableBulkAction', 'callTableBulkAction', 'assertDispatched', 'assertNotDispatched', 'assertDispatchedTo'], strict: true);
            }
        }

        return (new ObjectType(Testable::class))->isSuperTypeOf(TypeCombinator::removeNull($this->getType($receiver)))->yes();
    }
}
