<?php

use Filament\Tests\TestCase;
use Filament\Widgets\WidgetConfiguration;

uses(TestCase::class);

it('defaults `getProperties()` to an empty array', function (): void {
    $configuration = new WidgetConfiguration(widget: 'App\\Widgets\\TestWidget');

    expect($configuration->getProperties())->toBe([]);
});

it('stores the widget class and properties', function (): void {
    $configuration = new WidgetConfiguration(
        widget: 'App\\Widgets\\TestWidget',
        properties: ['key' => 'value'],
    );

    expect($configuration->widget)->toBe('App\\Widgets\\TestWidget');
    expect($configuration->getProperties())->toBe(['key' => 'value']);
});
