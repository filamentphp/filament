<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralExpressions;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

function components(array &$events, bool &$condition): array
{
    $preview = function (BaseFileUpload $component) use (&$events, &$condition): bool {
        $events[] = $component->getName();

        return $condition;
    };

    $ratio = function () use (&$events): string {
        $events[] = 'ratio';

        return '16:9';
    };

    return [
        FileUpload::make('preview')->image()->disablePreview($preview),
        FileUpload::make('snapshot')->disablePreview($condition),
        FileUpload::make('crop')->imageCropAspectRatio($ratio),
        TextInput::make('autocomplete')->disableAutocomplete(fn (): bool => $condition),
        Builder::make('items')->schema([])->label('original')->disableItemCreation(fn (Builder $component): bool => $component->getLabel() === 'original'),
        Select::make('status')->options([])->label('original')->disablePlaceholderSelection(fn (Select $component): bool => $component->getLabel() === 'original'),
        Action::make('eager')->modalAlignment(Alignment::End)->centerModal(function () use (&$events): bool { $events[] = 'eager'; return false; }),
        CreateAction::make()->disableCreateAnother(fn (CreateAction $action): bool => $action->getName() === 'create'),
        DateTimePicker::make('date')->icon(Heroicon::Clock),
        Builder::make('inset')->inset(function () use (&$events): bool { $events[] = 'unused'; return true; }),
        Repeater::make('repeated')->label('original')->disableItemCreation(fn (Repeater $component): bool => $component->getLabel() === 'original')->disableItemDeletion(fn (Repeater $component): bool => $component->getLabel() === 'original')->disableItemMovement(fn (Repeater $component): bool => $component->getLabel() === 'original'),
        EditAction::make()->centerModal(condition: fn (EditAction $action): bool => $action->getName() === 'edit'),
        KeyValue::make('metadata')->label('original')->disableEditingKeys(fn (KeyValue $component): bool => $component->getLabel() === 'original'),
    ];
}

function configureTyped(FileUpload $upload, Action $action): void
{
    $upload->disablePreview(false);
    $action->centerModal(true);
}

function globallyConfigured(bool | \Closure $condition): FileUpload
{
    return FileUpload::configureUsing(
        static fn (FileUpload $upload): FileUpload => $upload->disablePreview($condition),
        during: static fn (): FileUpload => FileUpload::make('global'),
    );
}

function stored(): Builder
{
    $builder = Builder::make('stored')->label('original');

    return $builder->disableItemCreation(fn (Builder $component): bool => $component->getLabel() === 'original');
}

function inlineClosure(bool &$condition, array &$events): FileUpload
{
    return FileUpload::make('inline')->disablePreview(function () use (&$condition, &$events): bool {
        $events[] = 'inline';

        return $condition;
    });
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralExpressions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

function components(array &$events, bool &$condition): array
{
    $preview = function (BaseFileUpload $component) use (&$events, &$condition): bool {
        $events[] = $component->getName();

        return $condition;
    };

    $ratio = function () use (&$events): string {
        $events[] = 'ratio';

        return '16:9';
    };

    return [
        FileUpload::make('preview')->image()->previewable(fn(BaseFileUpload $configurationComponent) => !$configurationComponent->evaluate($preview)),
        FileUpload::make('snapshot')->previewable(!$condition),
        FileUpload::make('crop')->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio(fn(FileUpload $configurationComponent) => \filled($configurationComponent->getImageAspectRatio())),
        TextInput::make('autocomplete')->autocomplete(fn () => $condition ? false : null),
        Builder::make('items')->schema([])->label('original')->addable(fn (Builder $component) => !($component->getLabel() === 'original')),
        Select::make('status')->options([])->label('original')->selectablePlaceholder(fn (Select $component) => !($component->getLabel() === 'original')),
        Action::make('eager')->modalAlignment(Alignment::End)->tap(function ($configurationComponent) use (&$events) {
            return $configurationComponent->evaluate(function () use (&$events): bool { $events[] = 'eager'; return false; }) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent;
        }),
        CreateAction::make()->createAnother(fn (CreateAction $action) => !($action->getName() === 'create')),
        DateTimePicker::make('date')->suffixIcon(Heroicon::Clock, isInline: true),
        Builder::make('inset'),
        Repeater::make('repeated')->label('original')->addable(fn (Repeater $component) => !($component->getLabel() === 'original'))->deletable(fn (Repeater $component) => !($component->getLabel() === 'original'))->reorderable(fn (Repeater $component) => !($component->getLabel() === 'original')),
        EditAction::make()->tap(fn($configurationComponent) => $configurationComponent->evaluate(fn (EditAction $action): bool => $action->getName() === 'edit') ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent),
        KeyValue::make('metadata')->label('original')->editableKeys(fn (KeyValue $component) => !($component->getLabel() === 'original')),
    ];
}

function configureTyped(FileUpload $upload, Action $action): void
{
    $upload->previewable(true);
    $action->modalAlignment(Alignment::Center);
}

function globallyConfigured(bool | Closure $condition): FileUpload
{
    return FileUpload::configureUsing(
        static fn (FileUpload $upload): FileUpload => $upload->previewable(fn(BaseFileUpload $configurationComponent) => !$configurationComponent->evaluate($condition)),
        during: static fn (): FileUpload => FileUpload::make('global'),
    );
}

function stored(): Builder
{
    $builder = Builder::make('stored')->label('original');

    return $builder->addable(fn (Builder $component) => !($component->getLabel() === 'original'));
}

function inlineClosure(bool &$condition, array &$events): FileUpload
{
    return FileUpload::make('inline')->previewable(function () use (&$condition, &$events) {
        $events[] = 'inline';

        return !$condition;
    });
}
