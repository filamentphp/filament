<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Widgets\JsWidget;
use Livewire\Attributes\Locked;

class JsWidgetBrowserTest extends Page
{
    use HasFiltersForm;

    protected string $view = 'pages.js-widget-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public bool $showWidget = true;

    #[Locked]
    public string $framework = 'js';

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->framework = request('framework', 'js');
        abort_unless(in_array($this->framework, ['js', 'js-ts', 'react', 'react-ts', 'vue', 'vue-ts', 'svelte', 'svelte-ts', 'inline', 'broken']), 404);
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('period')->default('All time')->live(),
        ]);
    }

    protected function getFooterWidgets(): array
    {
        return $this->showWidget ? [JsWidget::make(['framework' => $this->framework])] : [];
    }
}
