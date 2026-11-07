<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfigurationManual;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

function configure(?FileUpload $upload, bool | Closure $condition): ?FileUpload
{
    return $upload?->label('Configured')->disablePreview($condition);
}

function ratio(FileUpload $upload, string | Closure | null $ratio): FileUpload
{
    return $upload->imageCropAspectRatio($ratio);
}

function icon(DateTimePicker $picker, string | false | null $icon): DateTimePicker
{
    return $picker->icon($icon);
}

function center(Action $action, bool | Closure | null $condition): Action
{
    return $action->centerModal($condition);
}

function nullableConfiguration(?FileUpload $upload, ?DateTimePicker $picker, ?Action $action, string | null $ratio, string | false | null $icon, bool $condition): void
{
    $upload?->imageCropAspectRatio($ratio);
    $upload?->label('Photo')->imageCropAspectRatio($ratio);
    $picker?->icon($icon);
    $picker?->label('Date')->icon($icon);
    $picker?->icon()->label('Default');
    $picker?->icon(false)->label('Unchanged');
    $action?->centerModal($condition);
    $action?->label('Edit')->centerModal($condition);
}

function nullableSelect(?Select $select, bool | Closure $condition): array
{
    return [
        $select?->disablePlaceholderSelection($condition)->label('After'),
        $select?->disablePlaceholderSelection(...[$condition]),
        $select?->disablePlaceholderSelection(...['condition' => $condition]),
        $select?->disablePlaceholderSelection(...[]),
        $select?->disablePlaceholderSelection(...[false]),
        $select?->disablePlaceholderSelection(...['condition' => true]),
    ];
}

class Configuration
{
    public FileUpload $upload;
    public DateTimePicker $picker;
    public Action $action;
}

function nullableProperties(?Configuration $configuration, string $ratio, string $icon, bool $condition): void
{
    $configuration?->upload->imageCropAspectRatio($ratio);
    $configuration?->picker->label('Date')->icon($icon);
    $configuration?->action->centerModal($condition);
}

function autocomplete(TextInput $input, bool | Closure $condition): TextInput
{
    return $input->disableAutocomplete($condition)->disableAutocapitalize($condition);
}

function unrelated(Other $component): void
{
    $component->disablePreview();
}

class Other
{
    public function disablePreview(): static { return $this; }
}

class CustomUpload extends FileUpload {}

class OverriddenUpload extends FileUpload
{
    public function disablePreview(bool | Closure $condition = true): static { return $this; }
}

class OverriddenPreview extends FileUpload
{
    public function previewable(bool | Closure $condition = true): static { return $this; }
}

function subclasses(CustomUpload $inherited, OverriddenUpload $legacy, OverriddenPreview $canonical): void
{
    $inherited->disablePreview(false);
    $legacy->disablePreview(false);
    $canonical->disablePreview(false);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfigurationManual;

use Filament\Forms\Components\BaseFileUpload;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Field;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

function configure(?FileUpload $upload, bool | Closure $condition): ?FileUpload
{
    return $upload?->label('Configured')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($condition));
}

function ratio(FileUpload $upload, string | Closure | null $ratio): FileUpload
{
    return (static fn(FileUpload $component, string|Closure|null $ratio): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))($upload, $ratio);
}

function icon(DateTimePicker $picker, string | false | null $icon): DateTimePicker
{
    return (static fn(DateTimePicker $component, string|BackedEnum|Htmlable|Closure|bool|null $icon): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))($picker, $icon);
}

function center(Action $action, bool | Closure | null $condition): Action
{
    return (static function (Action $action, bool|Closure|null $condition): Action {
        if ($action->evaluate($condition)) {
            $action->modalAlignment(Alignment::Center);
        }
        return $action;
    })($action, $condition);
}

function nullableConfiguration(?FileUpload $upload, ?DateTimePicker $picker, ?Action $action, string | null $ratio, string | false | null $icon, bool $condition): void
{
    $upload?->tap((static fn(string|Closure|null $ratio): Closure => static fn(FileUpload $component): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))($ratio));
    $upload?->label('Photo')->tap((static fn(string|Closure|null $ratio): Closure => static fn(FileUpload $component): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))($ratio));
    $picker?->tap((static fn(string|BackedEnum|Htmlable|Closure|bool|null $icon): Closure => static fn(DateTimePicker $component): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))($icon));
    $picker?->label('Date')->tap((static fn(string|BackedEnum|Htmlable|Closure|bool|null $icon): Closure => static fn(DateTimePicker $component): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))($icon));
    $picker?->tap((static fn(string|BackedEnum|Htmlable|Closure|bool|null $icon): Closure => static fn(DateTimePicker $component): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))(null))->label('Default');
    $picker?->tap((static fn(string|BackedEnum|Htmlable|Closure|bool|null $icon): Closure => static fn(DateTimePicker $component): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))(false))->label('Unchanged');
    $action?->centerModal($condition);
    $action?->label('Edit')->centerModal($condition);
}

function nullableSelect(?Select $select, bool | Closure $condition): array
{
    return [
        $select?->tap((static fn(bool|Closure $condition): Closure => static fn(Select $component): Select => $component->selectablePlaceholder(static fn(): bool => !$component->evaluate($condition)))($condition))->label('After'),
        $select?->tap((static fn(bool|Closure $condition): Closure => static fn(Select $component): Select => $component->selectablePlaceholder(static fn(): bool => !$component->evaluate($condition)))($condition)),
        $select?->tap((static fn(bool|Closure $condition): Closure => static fn(Select $component): Select => $component->selectablePlaceholder(static fn(): bool => !$component->evaluate($condition)))($condition)),
        $select?->selectablePlaceholder(false),
        $select?->selectablePlaceholder(true),
        $select?->selectablePlaceholder(false),
    ];
}

class Configuration
{
    public FileUpload $upload;
    public DateTimePicker $picker;
    public Action $action;
}

function nullableProperties(?Configuration $configuration, string $ratio, string $icon, bool $condition): void
{
    $configuration?->upload->tap((static fn(string|Closure|null $ratio): Closure => static fn(FileUpload $component): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))($ratio));
    $configuration?->picker->label('Date')->tap((static fn(string|BackedEnum|Htmlable|Closure|bool|null $icon): Closure => static fn(DateTimePicker $component): DateTimePicker => $icon === false ? $component : $component->suffixIcon($icon ?? Heroicon::Calendar, isInline: true))($icon));
    $configuration?->action->centerModal($condition);
}

function autocomplete(TextInput $input, bool | Closure $condition): TextInput
{
    return $input->autocomplete((static fn(bool|Closure $condition): Closure => static fn(Field $component): ?bool => $component->evaluate($condition) ? false : null)($condition))->autocapitalize((static fn(bool|Closure $condition): Closure => static fn(Field $component): ?bool => $component->evaluate($condition) ? false : null)($condition));
}

function unrelated(Other $component): void
{
    $component->disablePreview();
}

class Other
{
    public function disablePreview(): static { return $this; }
}

class CustomUpload extends FileUpload {}

class OverriddenUpload extends FileUpload
{
    public function disablePreview(bool | Closure $condition = true): static { return $this; }
}

class OverriddenPreview extends FileUpload
{
    public function previewable(bool | Closure $condition = true): static { return $this; }
}

function subclasses(CustomUpload $inherited, OverriddenUpload $legacy, OverriddenPreview $canonical): void
{
    $inherited->previewable(true);
    $legacy->disablePreview(false);
    $canonical->disablePreview(false);
}
