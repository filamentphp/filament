<?php

use Filament\Tests\Fixtures\Widgets\JsWidget;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders JavaScript widgets without a custom Blade view and synchronizes `getRendererProps()`', function (): void {
    $widget = livewire(JsWidget::class, ['framework' => 'inline', 'pageFilters' => ['period' => 'September']])
        ->assertSee('jsWidgetComponent', escape: false)
        ->assertSee('September: 19');

    preg_match('/wire:key="([^"]+\.renderer\.[^"]+)"/', $widget->html(), $initialRendererKey);
    preg_match('/wire:key="([^"]+\.renderer-props\.[^"]+)"/', $widget->html(), $initialPropsKey);

    $widget->call('refreshTotal')->assertSee('September: 0');

    preg_match('/wire:key="([^"]+\.renderer\.[^"]+)"/', $widget->html(), $updatedRendererKey);
    preg_match('/wire:key="([^"]+\.renderer-props\.[^"]+)"/', $widget->html(), $updatedPropsKey);

    expect($initialRendererKey[1])->toBe($updatedRendererKey[1]);
    expect($initialPropsKey[1])->not->toBe($updatedPropsKey[1]);
});

it('escapes renderer props without treating their content as markup', function (): void {
    livewire(JsWidget::class, ['framework' => 'inline', 'pageFilters' => ['period' => '"><script>alert(1)</script>']])
        ->assertDontSee('<script>alert(1)</script>', escape: false)
        ->assertSee('&lt;script&gt;', escape: false);
});
