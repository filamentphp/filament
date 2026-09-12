<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Concerns\HasJsRenderer;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\JsField;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\RawJs;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can configure and unset `renderer()` with utility injection', function (): void {
    $field = JsField::make('location');

    expect($field->getRenderer())->toBeNull();

    $field->renderer(static fn (JsField $component): RawJs => RawJs::make('window.' . $component->getName()));

    expect((string) $field->getRenderer())->toBe('window.location');
    $field->renderer(static fn (JsField $component): string => '/build/' . $component->getName() . '.js');
    expect($field->getRenderer())->toBe('/build/location.js');
    expect($field->renderer(null)->getRenderer())->toBeNull();
});

it('evaluates and clears `rendererProps()` with utility injection', function (): void {
    $field = JsField::make('location');
    expect($field->getRendererProps())->toBe([]);
    $field->rendererProps(static fn (JsField $component): array => ['name' => $component->getName(), 'options' => ['zoom' => 12]]);
    expect($field->getRendererProps())->toBe(['name' => 'location', 'options' => ['zoom' => 12]]);
    expect($field->rendererProps(null)->getRendererProps())->toBe([]);
});

it('renders `JsField` and independent fields using `HasJsRenderer` with embedded wrappers', function (): void {
    expect(is_subclass_of(PluginLocationField::class, JsField::class))->toBeFalse();

    livewire(RenderJsField::class)
        ->assertSuccessful()
        ->assertSeeHtml('jsFieldFormComponent')
        ->assertSeeHtml('data-plugin-field="true"')
        ->assertSee('Plugin location')
        ->fillForm([
            'draft' => ['title' => 'Café / revised', 'enabled' => false],
            'location' => ['latitude' => 51.5, 'longitude' => -0.12],
        ])
        ->assertSchemaStateSet([
            'draft' => ['title' => 'Café / revised', 'enabled' => false],
            'location' => ['latitude' => 51.5, 'longitude' => -0.12],
        ]);
});

it('renders effective `stateBindingModifiers()` instead of overridden `live()` options', function (array $modifiers, bool $live, bool $blur, ?int $debounce): void {
    $field = JsField::make('location')->live(debounce: 42)->stateBindingModifiers($modifiers);
    $html = Schema::make(Livewire::make())->statePath('data')->components([$field])->toHtml();

    expect($html)
        ->toContain('\\u0022isLive\\u0022:' . ($live ? 'true' : 'false'))
        ->toContain('\\u0022isLiveOnBlur\\u0022:' . ($blur ? 'true' : 'false'))
        ->toContain('\\u0022debounce\\u0022:' . ($debounce ?? 'null'));
})->with([
    'deferred override' => [[], false, false, null],
    'live override' => [['live'], true, false, null],
    'blur override' => [['blur'], true, true, null],
    'default debounce' => [['live', 'debounce'], true, false, 150],
    'milliseconds' => [['live', 'debounce', '700ms'], true, false, 700],
    'seconds' => [['live', 'debounce', '1s'], true, false, 1000],
    'zero debounce' => [['live', 'debounce', 0], true, false, 0],
]);

class RenderJsField extends Livewire
{
    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            JsField::make('draft')->renderer(RawJs::make('window.draftRenderer')),
            PluginLocationField::make('location')->label('Plugin location')->extraAttributes(['data-plugin-field' => 'true']),
        ]);
    }
}

class PluginLocationField extends Field implements HasEmbeddedView
{
    use HasJsRenderer;

    public function getRenderer(): string
    {
        return '/plugins/location.js';
    }
}
