<?php

namespace Filament\Upgrade\Rector;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedFormComponentMethodsRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const METHOD_RENAMES = [
        BaseFileUpload::class => [
            'enableDownload' => 'downloadable',
            'enableOpen' => 'openable',
            'enableReordering' => 'reorderable',
            'moveFile' => 'moveFiles',
            'storeFile' => 'storeFiles',
        ],
        Builder::class => [
            'createItemBetweenButtonLabel' => 'addBetweenActionLabel',
            'createItemButtonLabel' => 'addActionLabel',
            'showBlockLabels' => 'blockLabels',
            'withBlockLabels' => 'blockLabels',
            'withBlockNumbers' => 'blockNumbers',
        ],
        Repeater::class => [
            'createItemButtonLabel' => 'addActionLabel',
            'orderable' => 'orderColumn',
        ],
        KeyValue::class => [
            'addButtonLabel' => 'addActionLabel',
            'deleteButtonLabel' => 'deleteActionLabel',
            'reorderButtonLabel' => 'reorderActionLabel',
        ],
        FileUpload::class => [
            'idleLabel' => 'placeholder',
            'imageResizeMode' => 'automaticallyResizeImagesMode',
            'imageResizeTargetHeight' => 'automaticallyResizeImagesToHeight',
            'imageResizeTargetWidth' => 'automaticallyResizeImagesToWidth',
            'imageResizeUpscale' => 'automaticallyUpscaleImagesWhenResizing',
            'orientImageFromExif' => 'orientImagesFromExif',
            'getImageCropAspectRatio' => 'getAutomaticallyCropImagesAspectRatio',
            'getImageResizeMode' => 'getAutomaticallyResizeImagesMode',
            'getImageResizeTargetHeight' => 'getAutomaticallyResizeImagesHeight',
            'getImageResizeTargetWidth' => 'getAutomaticallyResizeImagesWidth',
            'getImageResizeUpscale' => 'shouldAutomaticallyUpscaleImagesWhenResizing',
            'imageEditorAspectRatios' => 'imageEditorAspectRatioOptions',
            'getImageEditorAspectRatiosForJs' => 'getImageEditorAspectRatioOptionsForJs',
        ],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament form component method calls', [
            new CodeSample(
                '$upload->enableDownload(condition: true);',
                '$upload->downloadable(condition: true);',
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
        $receiverType = TypeCombinator::removeNull($this->getType($node->var));

        foreach (self::METHOD_RENAMES as $componentClass => $methodRenames) {
            $methodRenames = array_change_key_case($methodRenames);

            if (! isset($methodRenames[$methodName])) {
                continue;
            }

            if (! (new ObjectType($componentClass))->isSuperTypeOf($receiverType)->yes()) {
                continue;
            }

            $candidateMethodNames = [$methodName, $methodRenames[$methodName]];

            if ($methodName === 'showblocklabels') {
                $candidateMethodNames[] = 'withBlockLabels';
            }

            // Custom overrides anywhere in the forwarding chain need manual migration.
            foreach ($receiverType->getObjectClassReflections() as $classReflection) {
                foreach ($candidateMethodNames as $candidateMethodName) {
                    if (
                        $classReflection->hasNativeMethod($candidateMethodName) &&
                        ($classReflection->getNativeMethod($candidateMethodName)->getDeclaringClass()->getName() !== $componentClass)
                    ) {
                        return null;
                    }
                }
            }

            if (($methodName === 'idlelabel') && (! $node->isFirstClassCallable())) {
                foreach ($node->getArgs() as $argument) {
                    if ($argument->unpack) {
                        return null;
                    }
                }

                foreach ($node->getArgs() as $argument) {
                    if ($argument->name?->toString() === 'label') {
                        $argument->name = new Identifier('placeholder');
                    }
                }
            }

            $node->name = new Identifier($methodRenames[$methodName]);

            return $node;
        }

        return null;
    }
}
