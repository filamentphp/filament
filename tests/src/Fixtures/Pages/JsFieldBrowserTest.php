<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\JsField;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class JsFieldBrowserTest extends Page
{
    protected string $view = 'pages.js-field-browser-test';

    public array $data = [];

    public bool $locked = false;

    public function mount(): void
    {
        $this->form->fill(['blur' => 'Original blur', 'debounce' => 'Original debounce']);
    }

    public function form(Schema $schema): Schema
    {
        $renderer = RawJs::make(<<<'JS'
            ({ host, props, utilities }) => {
                host.utilities = utilities
                const input = document.createElement("input")
                input.id = props.id
                input.oninput = () => props.onChange(input.value)
                input.onblur = props.onBlur
                host.append(input)
                return {
                    update: (next) => {
                        input.value = next.value ?? ''
                        input.disabled = next.disabled
                        input.readOnly = next.readOnly
                    },
                    destroy: () => { input.oninput = null; input.onblur = null; input.remove() },
                }
            }
            JS);

        return $schema->statePath('data')->components([
            JsField::make('blur')->renderer($renderer)->live(onBlur: true)->disabled(fn (): bool => $this->locked),
            JsField::make('debounce')->renderer($renderer)->live(debounce: 300)->readOnly(fn (): bool => $this->locked),
        ]);
    }
}
