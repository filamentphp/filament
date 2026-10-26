<?php

namespace Filament\Upgrade\Rector;

use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Tables\Columns\ImageColumn;
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

class RenameDeprecatedImagePresentationMethodsRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const METHOD_RENAMES = [
        ImageEntry::class => [
            'height' => 'imageHeight',
            'size' => 'imageSize',
            'width' => 'imageWidth',
            'getHeight' => 'getImageHeight',
            'getWidth' => 'getImageWidth',
        ],
        ImageColumn::class => [
            'height' => 'imageHeight',
            'rounded' => 'circular',
            'size' => 'imageSize',
            'getHeight' => 'getImageHeight',
            'isRounded' => 'isCircular',
        ],
        KeyValueEntry::class => [
            'emptyMessage' => 'placeholder',
        ],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament image and infolist presentation method calls', [
            new CodeSample(
                '$entry->emptyMessage(message: "No data");',
                '$entry->placeholder(placeholder: "No data");',
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

        foreach (self::METHOD_RENAMES as $componentClass => $methodRenames) {
            $methodRenames = array_change_key_case($methodRenames);

            if (! isset($methodRenames[$methodName])) {
                continue;
            }

            if (! (new ObjectType($componentClass))->isSuperTypeOf($receiverType)->yes()) {
                continue;
            }

            // `placeholder()` is inherited from `Entry`, unlike the image methods.
            $replacementDeclaringClass = ($componentClass === KeyValueEntry::class) ? Entry::class : $componentClass;

            // Custom overrides need manual migration to preserve parameters and dispatch.
            foreach ($receiverType->getObjectClassReflections() as $classReflection) {
                foreach ([$methodName => $componentClass, $methodRenames[$methodName] => $replacementDeclaringClass] as $candidateMethodName => $declaringClass) {
                    if (
                        $classReflection->hasNativeMethod($candidateMethodName) &&
                        ($classReflection->getNativeMethod($candidateMethodName)->getDeclaringClass()->getName() !== $declaringClass)
                    ) {
                        return null;
                    }
                }
            }

            if (($methodName === 'emptymessage') && (! $node->isFirstClassCallable())) {
                foreach ($node->getArgs() as $argument) {
                    if ($argument->unpack) {
                        return null;
                    }
                }

                foreach ($node->getArgs() as $argument) {
                    if ($argument->name?->toString() === 'message') {
                        $argument->name = new Identifier('placeholder');
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
