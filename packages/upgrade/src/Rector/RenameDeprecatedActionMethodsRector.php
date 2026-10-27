<?php

namespace Filament\Upgrade\Rector;

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
        'modalActions' => 'modalFooterActions',
        'extraModalActions' => 'extraModalFooterActions',
        'modalButton' => 'modalSubmitActionLabel',
        'modalFooter' => 'modalContentFooter',
        'modalSubheading' => 'modalDescription',
        'makeExtraModalAction' => 'makeModalSubmitAction',
        'failureNotificationMessage' => 'failureNotificationTitle',
        'successNotificationMessage' => 'successNotificationTitle',
        'withAttributes' => 'extraAttributes',
        'hold' => 'halt',
        'infolist' => 'schema',
        'disableLabel' => 'hiddenLabel',
        'afterReplicaSaved' => 'after',
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

        $receiverType = $this->resolveReceiverType($node->var);

        $actionClass = match (true) {
            $methodName === 'afterreplicasaved' => 'Filament\\Actions\\ReplicateAction',
            ($methodName === 'disablelabel') && (new ObjectType('Filament\\Actions\\ActionGroup'))->isSuperTypeOf($receiverType)->yes() => 'Filament\\Actions\\ActionGroup',
            default => 'Filament\\Actions\\Action',
        };

        if (! (new ObjectType($actionClass))->isSuperTypeOf($receiverType)->yes()) {
            return null;
        }

        // These wrappers did not forward extra arguments to the replacement's optional parameters.
        if (in_array($methodName, ['hold', 'withattributes'], strict: true)) {
            if ($node->isFirstClassCallable() || (count($node->getArgs()) > (($methodName === 'hold') ? 0 : 1))) {
                return null;
            }

            foreach ($node->getArgs() as $argument) {
                if ($argument->unpack) {
                    return null;
                }
            }
        }

        // Custom overrides need manual migration, not a call redirected past their behavior.
        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            foreach ([$methodName, $methodRenames[$methodName]] as $candidateMethodName) {
                if (! $classReflection->hasNativeMethod($candidateMethodName)) {
                    if ($candidateMethodName === $methodName) {
                        continue;
                    }

                    return null;
                }

                $methodReflection = $classReflection->getNativeMethod($candidateMethodName);
                $declaringClass = (($actionClass === 'Filament\\Actions\\ReplicateAction') && ($candidateMethodName === 'after')) ? 'Filament\\Actions\\Action' : $actionClass;

                if ((! $methodReflection->isPublic()) || ($methodReflection->getDeclaringClass()->getName() !== $declaringClass)) {
                    return null;
                }
            }
        }

        $renamedArgument = match ($methodName) {
            'form' => ['form', 'schema'],
            'infolist' => ['infolist', 'schema'],
            'modalsubheading' => ['subheading', 'description'],
            'failurenotificationmessage', 'successnotificationmessage' => ['message', 'title'],
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
