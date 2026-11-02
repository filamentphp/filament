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
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralExpressions;

use Closure;
use Filament\Forms\Components\Field;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
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
        FileUpload::make('preview')->image()->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($preview)),
        FileUpload::make('snapshot')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($condition)),
        (static fn(FileUpload $component, string|Closure|null $ratio): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))(FileUpload::make('crop'), $ratio),
        TextInput::make('autocomplete')->autocomplete((static fn(bool|Closure $condition): Closure => static fn(Field $component): ?bool => $component->evaluate($condition) ? false : null)(fn (): bool => $condition)),
        (static fn(Builder $component, bool|Closure $condition): Builder => $component->addable(static fn(): bool => !$component->evaluate($condition)))(Builder::make('items')->schema([])->label('original'), fn (Builder $component): bool => $component->getLabel() === 'original'),
        (static fn(Select $component, bool|Closure $condition): Select => $component->selectablePlaceholder(static fn(): bool => !$component->evaluate($condition)))(Select::make('status')->options([])->label('original'), fn (Select $component): bool => $component->getLabel() === 'original'),
        (static function (Action $action, bool|Closure|null $condition): Action {
            if ($action->evaluate($condition)) {
                $action->modalAlignment(Alignment::Center);
            }
            return $action;
        })(Action::make('eager')->modalAlignment(Alignment::End), function () use (&$events): bool { $events[] = 'eager'; return false; }),
        CreateAction::make()->createAnother((static fn(bool|Closure $condition): Closure => static fn(CreateAction $action): bool => !$action->evaluate($condition))(fn (CreateAction $action): bool => $action->getName() === 'create')),
        (static fn(DateTimePicker $component, string|BackedEnum|Htmlable|Closure|bool|null $icon): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))(DateTimePicker::make('date'), Heroicon::Clock),
        Builder::make('inset'),
        (static fn(Repeater $component, bool|Closure $condition): Repeater => $component->reorderable(static fn(): bool => !$component->evaluate($condition)))((static fn(Repeater $component, bool|Closure $condition): Repeater => $component->deletable(static fn(): bool => !$component->evaluate($condition)))((static fn(Repeater $component, bool|Closure $condition): Repeater => $component->addable(static fn(): bool => !$component->evaluate($condition)))(Repeater::make('repeated')->label('original'), fn (Repeater $component): bool => $component->getLabel() === 'original'), fn (Repeater $component): bool => $component->getLabel() === 'original'), fn (Repeater $component): bool => $component->getLabel() === 'original'),
        (static function (EditAction $action, bool|Closure|null $condition): EditAction {
            if ($action->evaluate($condition)) {
                $action->modalAlignment(Alignment::Center);
            }
            return $action;
        })(EditAction::make(), fn (EditAction $action): bool => $action->getName() === 'edit'),
        KeyValue::make('metadata')->label('original')->editableKeys((static fn(bool|Closure $condition): Closure => static fn(KeyValue $component): bool => !$component->evaluate($condition))(fn (KeyValue $component): bool => $component->getLabel() === 'original')),
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
        static fn (FileUpload $upload): FileUpload => $upload->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($condition)),
        during: static fn (): FileUpload => FileUpload::make('global'),
    );
}

function stored(): Builder
{
    $builder = Builder::make('stored')->label('original');

    return (static fn(Builder $component, bool|Closure $condition): Builder => $component->addable(static fn(): bool => !$component->evaluate($condition)))($builder, fn (Builder $component): bool => $component->getLabel() === 'original');
}
