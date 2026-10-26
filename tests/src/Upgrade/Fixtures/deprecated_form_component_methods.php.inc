<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormComponentMethods;

use Closure;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

function configure(BaseFileUpload $base, FileUpload $upload, Builder $builder, Repeater $repeater, KeyValue $keyValue, ?FileUpload $nullableUpload, array $arguments): void
{
    $base->enableDownload(condition: fn (): bool => false)
        ->enableOpen(condition: false)
        ->enableReordering()
        ->moveFile(condition: false)
        ->storeFile();
    $upload->enableDownload()->enableOpen()->enableReordering()->moveFile()->storeFile();
    $builder->createItemBetweenButtonLabel(label: fn (): string => 'Insert')
        ->createItemButtonLabel(label: null)
        ->showBlockLabels(condition: false)
        ->withBlockLabels()
        ->withBlockNumbers(condition: fn (): bool => true);
    $repeater->createItemButtonLabel(label: 'Add row')->orderable(column: 'position');
    $repeater->orderable();
    $keyValue->addButtonLabel(label: 'Add')->deleteButtonLabel(label: null)->reorderButtonLabel(label: fn (): string => 'Move');
    $upload->idleLabel(label: fn (): string => 'Upload')
        ->imageResizeMode(mode: 'contain')
        ->imageResizeTargetHeight(height: fn (): string => '240')
        ->imageResizeTargetWidth(width: '640')
        ->imageResizeUpscale(condition: false)
        ->orientImageFromExif()
        ->imageEditorAspectRatios(ratios: [null, '16:9']);
    $upload->getImageCropAspectRatio();
    $upload->getImageResizeMode();
    $upload->getImageResizeTargetHeight();
    $upload->getImageResizeTargetWidth();
    $upload->getImageResizeUpscale();
    $upload->getImageEditorAspectRatiosForJs();
    $nullableUpload?->enableDownload(condition: false);
    $nullableUpload?->idleLabel(label: null);
    $nullableUpload?->getImageResizeMode();
    $callback = $upload->enableOpen(...);
    $callback = $upload->idleLabel(...);
    $upload->ENABLEDOWNLOAD();
    $upload->enableOpen(...$arguments);
    $upload->idleLabel(...$arguments);
    $upload->downloadable();
    $method = 'enableOpen';
    $upload->{$method}();
    $upload->{'enableOpen'}();
    $callback = [$upload, 'enableOpen'];
    FileUpload::enableOpen();
    $upload->disablePreview()->imageCropAspectRatio('16:9');
    $builder->disableItemCreation()->disableItemDeletion()->disableItemMovement()->inset();
    $repeater->disableItemCreation()->disableItemDeletion()->disableItemMovement()->inset();
    $keyValue->disableAddingRows()->disableDeletingRows()->disableEditingKeys()->disableEditingValues();
}

class CustomUpload extends FileUpload
{
    public function configureUpload(): static
    {
        $this->enableDownload();

        return parent::enableOpen();
    }
}

class AliasOverridingUpload extends FileUpload
{
    public function enableDownload(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedAliasOverridingUpload extends AliasOverridingUpload {}

class ReplacementOverridingUpload extends FileUpload
{
    public function placeholder(string | Closure | null $text): static
    {
        return $this;
    }

    public function configureUpload(): static
    {
        return $this->idleLabel(label: 'Upload');
    }
}

class InheritedReplacementOverridingUpload extends ReplacementOverridingUpload {}

class IntermediateOverridingBuilder extends Builder
{
    public function withBlockLabels(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedIntermediateOverridingBuilder extends IntermediateOverridingBuilder {}

class GetterOverridingUpload extends FileUpload
{
    public function getImageResizeMode(): ?string
    {
        return 'contain';
    }
}

function subclasses(CustomUpload $custom, ?InheritedAliasOverridingUpload $alias, InheritedReplacementOverridingUpload $replacement, InheritedIntermediateOverridingBuilder $builder, GetterOverridingUpload $getter, CustomUpload | AliasOverridingUpload $union): void
{
    $custom->enableDownload()->idleLabel(label: 'Upload');
    $alias?->enableDownload(enabled: false);
    $replacement->idleLabel(label: 'Upload');
    $builder->showBlockLabels();
    $getter->getImageResizeMode();
    $union->enableDownload();
}

function unrelated(TextInput $input, UnrelatedComponent $component, FileUpload | UnrelatedComponent $union, $unknown): void
{
    $input->idleLabel(label: 'Text');
    $component->enableDownload();
    $component->createItemButtonLabel(label: 'Add');
    $union->enableDownload();
    $unknown->enableDownload();
}

class UnrelatedComponent
{
    public function enableDownload(): void {}

    public function createItemButtonLabel(?string $label): void {}
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormComponentMethods;

use Closure;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

function configure(BaseFileUpload $base, FileUpload $upload, Builder $builder, Repeater $repeater, KeyValue $keyValue, ?FileUpload $nullableUpload, array $arguments): void
{
    $base->downloadable(condition: fn (): bool => false)
        ->openable(condition: false)
        ->reorderable()
        ->moveFiles(condition: false)
        ->storeFiles();
    $upload->downloadable()->openable()->reorderable()->moveFiles()->storeFiles();
    $builder->addBetweenActionLabel(label: fn (): string => 'Insert')
        ->addActionLabel(label: null)
        ->blockLabels(condition: false)
        ->blockLabels()
        ->blockNumbers(condition: fn (): bool => true);
    $repeater->addActionLabel(label: 'Add row')->orderColumn(column: 'position');
    $repeater->orderColumn();
    $keyValue->addActionLabel(label: 'Add')->deleteActionLabel(label: null)->reorderActionLabel(label: fn (): string => 'Move');
    $upload->placeholder(placeholder: fn (): string => 'Upload')
        ->automaticallyResizeImagesMode(mode: 'contain')
        ->automaticallyResizeImagesToHeight(height: fn (): string => '240')
        ->automaticallyResizeImagesToWidth(width: '640')
        ->automaticallyUpscaleImagesWhenResizing(condition: false)
        ->orientImagesFromExif()
        ->imageEditorAspectRatioOptions(ratios: [null, '16:9']);
    $upload->getAutomaticallyCropImagesAspectRatio();
    $upload->getAutomaticallyResizeImagesMode();
    $upload->getAutomaticallyResizeImagesHeight();
    $upload->getAutomaticallyResizeImagesWidth();
    $upload->shouldAutomaticallyUpscaleImagesWhenResizing();
    $upload->getImageEditorAspectRatioOptionsForJs();
    $nullableUpload?->downloadable(condition: false);
    $nullableUpload?->placeholder(placeholder: null);
    $nullableUpload?->getAutomaticallyResizeImagesMode();
    $callback = $upload->openable(...);
    $callback = $upload->placeholder(...);
    $upload->downloadable();
    $upload->openable(...$arguments);
    $upload->idleLabel(...$arguments);
    $upload->downloadable();
    $method = 'enableOpen';
    $upload->{$method}();
    $upload->{'enableOpen'}();
    $callback = [$upload, 'enableOpen'];
    FileUpload::enableOpen();
    $upload->disablePreview()->imageCropAspectRatio('16:9');
    $builder->disableItemCreation()->disableItemDeletion()->disableItemMovement()->inset();
    $repeater->disableItemCreation()->disableItemDeletion()->disableItemMovement()->inset();
    $keyValue->disableAddingRows()->disableDeletingRows()->disableEditingKeys()->disableEditingValues();
}

class CustomUpload extends FileUpload
{
    public function configureUpload(): static
    {
        $this->downloadable();

        return parent::enableOpen();
    }
}

class AliasOverridingUpload extends FileUpload
{
    public function enableDownload(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedAliasOverridingUpload extends AliasOverridingUpload {}

class ReplacementOverridingUpload extends FileUpload
{
    public function placeholder(string | Closure | null $text): static
    {
        return $this;
    }

    public function configureUpload(): static
    {
        return $this->idleLabel(label: 'Upload');
    }
}

class InheritedReplacementOverridingUpload extends ReplacementOverridingUpload {}

class IntermediateOverridingBuilder extends Builder
{
    public function withBlockLabels(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedIntermediateOverridingBuilder extends IntermediateOverridingBuilder {}

class GetterOverridingUpload extends FileUpload
{
    public function getImageResizeMode(): ?string
    {
        return 'contain';
    }
}

function subclasses(CustomUpload $custom, ?InheritedAliasOverridingUpload $alias, InheritedReplacementOverridingUpload $replacement, InheritedIntermediateOverridingBuilder $builder, GetterOverridingUpload $getter, CustomUpload | AliasOverridingUpload $union): void
{
    $custom->downloadable()->placeholder(placeholder: 'Upload');
    $alias?->enableDownload(enabled: false);
    $replacement->idleLabel(label: 'Upload');
    $builder->showBlockLabels();
    $getter->getImageResizeMode();
    $union->enableDownload();
}

function unrelated(TextInput $input, UnrelatedComponent $component, FileUpload | UnrelatedComponent $union, $unknown): void
{
    $input->idleLabel(label: 'Text');
    $component->enableDownload();
    $component->createItemButtonLabel(label: 'Add');
    $union->enableDownload();
    $unknown->enableDownload();
}

class UnrelatedComponent
{
    public function enableDownload(): void {}

    public function createItemButtonLabel(?string $label): void {}
}
