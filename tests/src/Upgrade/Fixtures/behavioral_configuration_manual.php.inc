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
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

function configure(?FileUpload $upload, bool | Closure $condition): ?FileUpload
{
    return $upload?->label('Configured')->previewable(fn(BaseFileUpload $configurationComponent) => !$configurationComponent->evaluate($condition));
}

function ratio(FileUpload $upload, string | Closure | null $ratio): FileUpload
{
    return $upload->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio(fn(FileUpload $configurationComponent) => \filled($configurationComponent->getImageAspectRatio()));
}

function icon(DateTimePicker $picker, string | false | null $icon): DateTimePicker
{
    return $picker->icon($icon);
}

function center(Action $action, bool | Closure | null $condition): Action
{
    return $action->tap(fn($configurationComponent) => $configurationComponent->evaluate($condition) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent);
}

function nullableConfiguration(?FileUpload $upload, ?DateTimePicker $picker, ?Action $action, string | null $ratio, string | false | null $icon, bool $condition): void
{
    $upload?->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio(fn(FileUpload $configurationComponent) => \filled($configurationComponent->getImageAspectRatio()));
    $upload?->label('Photo')->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio(fn(FileUpload $configurationComponent) => \filled($configurationComponent->getImageAspectRatio()));
    $picker?->icon($icon);
    $picker?->label('Date')->icon($icon);
    $picker?->suffixIcon(Heroicon::Calendar, isInline: true)->label('Default');
    $picker?->tap(static fn() => null)->label('Unchanged');
    $action?->tap(fn($configurationComponent) => $configurationComponent->evaluate($condition) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent);
    $action?->label('Edit')->tap(fn($configurationComponent) => $configurationComponent->evaluate($condition) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent);
}

function nullableSelect(?Select $select, bool | Closure $condition): array
{
    return [
        $select?->selectablePlaceholder(fn(Select $configurationComponent) => !$configurationComponent->evaluate($condition))->label('After'),
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
    $configuration?->upload->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio(fn(FileUpload $configurationComponent) => \filled($configurationComponent->getImageAspectRatio()));
    $configuration?->picker->label('Date')->suffixIcon($icon, isInline: true);
    $configuration?->action->tap(fn($configurationComponent) => $configurationComponent->evaluate($condition) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent);
}

function autocomplete(TextInput $input, bool | Closure $condition): TextInput
{
    return $input->autocomplete(fn(TextInput $configurationComponent) => $configurationComponent->evaluate($condition) ? false : null)->autocapitalize(fn(TextInput $configurationComponent) => $configurationComponent->evaluate($condition) ? false : null);
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
