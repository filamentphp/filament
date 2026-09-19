<?php

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasJsRenderer;
use Filament\Schemas\Components\JsComponent;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('evaluates and clears `renderer()` and `rendererProps()`', function (): void {
    $component = JsComponent::make()->id('sales');
    expect($component)->not->toBeInstanceOf(Field::class);
    expect($component->getRenderer())->toBeNull();
    expect($component->getRendererProps())->toBe([]);

    $component->renderer(static fn (JsComponent $component): string => '/' . $component->getId() . '.js')
        ->rendererProps(static fn (JsComponent $component): array => ['message' => $component->getId(), 'options' => ['enabled' => false]]);
    expect($component->getRenderer())->toBe('/sales.js');
    expect($component->getRendererProps())->toBe(['message' => 'sales', 'options' => ['enabled' => false]]);
    expect((string) $component->renderer(RawJs::make('window.renderSales'))->getRenderer())->toBe('window.renderSales');
    expect($component->renderer(null)->getRenderer())->toBeNull();
    expect($component->rendererProps(null)->getRendererProps())->toBe([]);
});

it('renders a JavaScript host without field bindings or child components', function (): void {
    $component = JsComponent::make()->key('sales')->renderer('/sales.js')
        ->rendererProps(['message' => '</span><script>alert(1)</script>'])
        ->extraAttributes(['data-sales' => 'true'])
        ->schema([Text::make('This child must not render')]);
    $html = Schema::make(Livewire::make())->components([$component])->toHtml();

    expect($html)->toContain('jsSchemaComponent', 'wire:ignore', 'data-renderer-props=', 'data-sales="true"', 'role="alert"')
        ->not->toContain('This child must not render', '$entangle', 'onChange', '<script>alert(1)</script>', 'data-field-wrapper');
});

it('updates only the props key when PHP configuration changes', function (): void {
    $component = JsComponent::make()->key('sales')->renderer('/sales.js')->rendererProps(['message' => 'Original']);
    Schema::make(Livewire::make())->components([$component])->getComponents();
    $original = $component->toHtml();
    $updated = $component->rendererProps(['message' => 'Updated'])->toHtml();
    preg_match('/wire:key="([^"]+)"/', $original, $originalKey);
    preg_match('/wire:key="([^"]+)"/', $updated, $updatedKey);

    expect($updatedKey[1])->toBe($originalKey[1]);
    expect($updated)->not->toBe($original);
    expect($component->renderer('/replacement.js')->toHtml())->not->toContain('wire:key="' . $originalKey[1] . '"');
});

it('supports reusable components using `HasJsRenderer` without extending `JsComponent`', function (): void {
    $component = new class extends Component
    {
        use HasJsRenderer;

        public function getRenderer(): string
        {
            return '/plugin.js';
        }
    };
    expect($component->getRendererProps())->toBe([]);
    expect(Schema::make(Livewire::make())->components([$component])->toHtml())->toContain('jsSchemaComponent');
});
