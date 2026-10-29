<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfigurationManual;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

function manual(FileUpload $upload, Action $action, bool $condition, array $arguments, string $method): void
{
    $upload->disablePreview();
    $action->centerModal(false);
    FileUpload::make('dynamic')->disablePreview($condition);
    FileUpload::make('injected')->disablePreview(fn (FileUpload $component): bool => $component->isDisabled());
    FileUpload::make('captured')->disablePreview(function () use (&$condition): bool { return $condition; });
    FileUpload::make('evaluated')->disablePreview($condition = random_int(0, 1) === 1);
    FileUpload::make('extra')->disablePreview(true, random_int(0, 1));
    FileUpload::make('named')->disablePreview(unknown: true);
    FileUpload::make('unpacked')->disablePreview(...$arguments);
    FileUpload::make('callable')->disablePreview(...);
    FileUpload::make('nullsafe')?->disablePreview();
    FileUpload::make('dynamic')->{$method}();
    FileUpload::make('tap')->tap(fn ($component) => $component)->disablePreview();
    FileUpload::make('macro')->icon()->disablePreview();
    FileUpload::make('ratio')->imageCropAspectRatio(ratio());
    FileUpload::make('ratio')->imageCropAspectRatio(fn (): string => '16:9');
    Action::make('eager')->centerModal(fn (): bool => $condition);
    Action::make('side-effect')->centerModal($condition = true);
    DateTimePicker::make('date')->icon(icon());
    DateTimePicker::make('date')->icon(true);
    Builder::make('blocks')->inset(sideEffect());
    Builder::make('blocks')->inset(fn () => sideEffect());
    Builder::make('blocks')->inset(true, sideEffect());
    Builder::make('blocks')->inset(...$arguments);
    Builder::make('blocks')->inset(...);
    TextInput::make('name')->disableAutocomplete(fn () => $condition);
    TextInput::make('name')->disableAutocapitalize($condition);
    CustomUpload::make('custom')->disablePreview();
    Other::make()->disablePreview();
}

class CustomUpload extends FileUpload
{
    public function configureLegacy(): static
    {
        parent::disablePreview();
        $this->disablePreview();

        return $this;
    }

    public function previewable(bool | Closure $condition = true): static
    {
        return $this;
    }
}

class Other
{
    public static function make(): static { return new static; }
    public function disablePreview(): static { return $this; }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfigurationManual;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Field;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

function manual(FileUpload $upload, Action $action, bool $condition, array $arguments, string $method): void
{
    $upload->previewable(false);
    $action;
    FileUpload::make('dynamic')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($condition));
    FileUpload::make('injected')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))(fn (FileUpload $component): bool => $component->isDisabled()));
    FileUpload::make('captured')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))(function () use (&$condition): bool { return $condition; }));
    FileUpload::make('evaluated')->previewable((static fn(bool|Closure $condition): Closure => static fn(BaseFileUpload $component): bool => !$component->evaluate($condition))($condition = random_int(0, 1) === 1));
    FileUpload::make('extra')->disablePreview(true, random_int(0, 1));
    FileUpload::make('named')->disablePreview(unknown: true);
    FileUpload::make('unpacked')->disablePreview(...$arguments);
    FileUpload::make('callable')->disablePreview(...);
    FileUpload::make('nullsafe')?->disablePreview();
    FileUpload::make('dynamic')->{$method}();
    FileUpload::make('tap')->tap(fn ($component) => $component)->disablePreview();
    FileUpload::make('macro')->icon()->disablePreview();
    FileUpload::make('ratio')->imageCropAspectRatio(ratio());
    (static fn(FileUpload $component, string|Closure|null $ratio): FileUpload => $component->imageAspectRatio($ratio)->automaticallyCropImagesToAspectRatio($ratio instanceof Closure ? $ratio : \filled($ratio)))(FileUpload::make('ratio'), fn (): string => '16:9');
    (static function (Action $action, bool|Closure|null $condition): Action {
        if ($action->evaluate($condition)) {
            $action->modalAlignment(Alignment::Center);
        }
        return $action;
    })(Action::make('eager'), fn (): bool => $condition);
    (static function (Action $action, bool|Closure|null $condition): Action {
        if ($action->evaluate($condition)) {
            $action->modalAlignment(Alignment::Center);
        }
        return $action;
    })(Action::make('side-effect'), $condition = true);
    DateTimePicker::make('date')->icon(icon());
    DateTimePicker::make('date')->icon(true);
    (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(Builder::make('blocks'))(sideEffect());
    (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(Builder::make('blocks'))(fn () => sideEffect());
    Builder::make('blocks')->inset(true, sideEffect());
    Builder::make('blocks')->inset(...$arguments);
    Builder::make('blocks')->inset(...);
    TextInput::make('name')->autocomplete((static fn(bool|Closure $condition): Closure => static fn(Field $component): ?bool => $component->evaluate($condition) ? false : null)(fn () => $condition));
    TextInput::make('name')->autocapitalize((static fn(bool|Closure $condition): Closure => static fn(Field $component): ?bool => $component->evaluate($condition) ? false : null)($condition));
    CustomUpload::make('custom')->disablePreview();
    Other::make()->disablePreview();
}

class CustomUpload extends FileUpload
{
    public function configureLegacy(): static
    {
        parent::disablePreview();
        $this->disablePreview();

        return $this;
    }

    public function previewable(bool | Closure $condition = true): static
    {
        return $this;
    }
}

class Other
{
    public static function make(): static { return new static; }
    public function disablePreview(): static { return $this; }
}
