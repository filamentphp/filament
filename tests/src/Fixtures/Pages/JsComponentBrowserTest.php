<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\JsComponent;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Livewire\Attributes\Locked;

class JsComponentBrowserTest extends Page
{
    protected string $view = 'pages.js-component-browser-test';

    #[Locked]
    public string $framework = 'js';

    public array $data = [];

    public bool $mounted = true;

    public bool $cleared = false;

    public bool $failed = false;

    public function mount(): void
    {
        $this->framework = request('framework', 'js');
        abort_unless(in_array($this->framework, ['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts']), 404);
        $this->form->fill(['caption' => 'Quarterly sales']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            TextInput::make('caption')->live(),
            JsComponent::make()->key('report')
                ->renderer($this->failed ? RawJs::make('() => { throw new Error("Expected test failure") }') : FilamentAsset::getScriptSrc('component-' . $this->framework, 'tests/js-components'))
                ->rendererConfiguration(fn (Get $get): array => $this->cleared ? [] : ['message' => $get('caption')])
                ->extraAttributes(['data-report' => 'true'])
                ->schema([Text::make('This child must not render')]),
            JsComponent::make()->key('comparison')
                ->renderer(FilamentAsset::getScriptSrc('component-' . $this->framework, 'tests/js-components'))
                ->rendererConfiguration(['message' => 'Previous quarter'])
                ->extraAttributes(['data-comparison' => 'true']),
        ]);
    }
}
