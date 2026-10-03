<?php

namespace Filament\Tests\Fixtures\Livewire;

use Livewire\Component;

class FilamentHtmlDirective extends Component
{
    public string $html = '<strong data-testid="filament-html-markup">Rendered as markup</strong><span data-testid="filament-html-nested-directive" x-text="\'Initialized\'">Not initialized</span>';

    public function replaceHtml(): void
    {
        $this->html = '<em data-testid="filament-html-replaced">Replaced</em>';
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                <div x-data>
                    <span data-testid="filament-html" x-filament-html="$wire.html"></span>
                </div>

                <button type="button" data-testid="filament-html-refresh" wire:click="$refresh">
                    Refresh
                </button>

                <button type="button" data-testid="filament-html-replace" wire:click="replaceHtml">
                    Replace
                </button>
            </div>
            BLADE;
    }
}
