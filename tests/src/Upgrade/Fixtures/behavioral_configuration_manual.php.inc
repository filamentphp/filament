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
