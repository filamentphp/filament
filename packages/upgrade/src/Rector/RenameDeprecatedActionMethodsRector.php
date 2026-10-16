<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedActionMethodsRector extends AbstractRector
{
    /**
     * @var array<string, string>
     */
    private const METHOD_RENAMES = [
        'emit' => 'dispatch',
        'emitSelf' => 'dispatchSelf',
        'emitTo' => 'dispatchTo',
        'disableForm' => 'disabledSchema',
        'disabledForm' => 'disabledSchema',
        'form' => 'schema',
        'getForm' => 'getSchema',
        'isFormDisabled' => 'isSchemaDisabled',
        'mutateFormDataUsing' => 'mutateDataUsing',
        'formData' => 'data',
        'resetFormData' => 'resetData',
        'getFormData' => 'getData',
        'getRawFormData' => 'getRawData',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament action method calls', [
            new CodeSample(
                "\Filament\Actions\Action::make('save')->form(form: []);",
                "\Filament\Actions\Action::make('save')->schema(schema: []);",
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
        $methodRenames = array_change_key_case(self::METHOD_RENAMES);

        if (! isset($methodRenames[$methodName])) {
            return null;
        }

        $receiverType = TypeCombinator::removeNull($this->getType($node->var));

        if (! (new ObjectType('Filament\\Actions\\Action'))->isSuperTypeOf($receiverType)->yes()) {
            return null;
        }

        // Custom overrides need manual migration, not a call redirected past their behavior.
        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            foreach ([$methodName, $methodRenames[$methodName]] as $candidateMethodName) {
                if (
                    $classReflection->hasNativeMethod($candidateMethodName) &&
                    ($classReflection->getNativeMethod($candidateMethodName)->getDeclaringClass()->getName() !== 'Filament\\Actions\\Action')
                ) {
                    return null;
                }
            }
        }

        if (($methodName === 'form') && (! $node->isFirstClassCallable())) {
            foreach ($node->getArgs() as $argument) {
                if ($argument->unpack) {
                    return null;
                }
            }

            foreach ($node->getArgs() as $argument) {
                if ($argument->name?->toString() === 'form') {
                    $argument->name = new Identifier('schema');
                }
            }
        }

        $node->name = new Identifier($methodRenames[$methodName]);

        return $node;
    }
}
