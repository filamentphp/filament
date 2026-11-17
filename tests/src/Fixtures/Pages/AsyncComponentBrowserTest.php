<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Concerns\HasLoadingPlaceholder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class AsyncComponentBrowserTest extends Page
{
    protected string $view = 'pages.async-component-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public bool $isShown = true;

    public string $control = '';

    public ?array $data = [];

    public function mount(): void
    {
        $this->control = request()->string('control')->toString();
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        if ($this->control !== '') {
            $field = match ($this->control) {
                'color-picker' => ColorPicker::make('value')->default('#a141b2'),
                'date-time-picker' => DateTimePicker::make('value')->native(false)->displayFormat('Y-m-d H:i')->default('2025-07-15 14:25:00'),
                'file-upload' => FileUpload::make('value'),
                'key-value' => KeyValue::make('value')->keyLabel('Property name')->default(['Name' => 'Filament']),
                'tags-input' => TagsInput::make('value')->autofocus()->default(['Filament', 'Laravel']),
            };

            return $schema->statePath('data')->components([$field->label('Test value')]);
        }

        $field = new class('value') extends Field
        {
            use HasLoadingPlaceholder;

            public function toEmbeddedHtml(): string
            {
                return $this->wrapAsyncComponentHtml(<<<'HTML'
                    <div
                        x-bind:inert="isLoading"
                        x-bind:aria-hidden="isLoading"
                        inert
                        aria-hidden="true"
                        class="fi-async-component-content"
                        x-load
                        x-load-src="/async-component.js"
                        x-data="asyncTestComponent()"
                    >
                        <label>
                            Test value
                            <input x-model="value" />
                        </label>
                    </div>
                    HTML, key: 'async-test-control');
            }
        };

        return $schema->components([
            $field->loadingPlaceholder(request()->boolean('custom')
                ? view('loading-placeholder', ['label' => 'Preparing control'])
                : null),
        ]);
    }
}
