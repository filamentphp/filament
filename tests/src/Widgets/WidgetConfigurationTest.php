<?php

use Filament\Tests\TestCase;
use Filament\Widgets\WidgetConfiguration;

uses(TestCase::class);

it('stores the widget class and properties', function (): void {
    $config = new WidgetConfiguration(
        widget: 'App\\Widgets\\TestWidget',
        properties: ['key' => 'value'],
    );

    expect($config->widget)->toBe('App\\Widgets\\TestWidget');
    expect($config->getProperties())->toBe(['key' => 'value']);
});

it('defaults properties to an empty array', function (): void {
    $config = new WidgetConfiguration(widget: 'App\\Widgets\\TestWidget');

    expect($config->getProperties())->toBe([]);
});
