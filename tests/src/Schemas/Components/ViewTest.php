<?php

use Filament\Schemas\Components\View;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can have view data', function (): void {
    $component = View::make('test')
        ->viewData([
            'key_a' => 'Value A',
            'key_b' => 'Value B',
        ])
        ->viewData([]);

    expect($component)
        ->getViewData()->toBe([
            'key_a' => 'Value A',
            'key_b' => 'Value B',
        ]);
});

it('can have view data inside closures', function (): void {
    $component = View::make('test')
        ->viewData(['key_a' => 'Value A'])
        ->viewData(fn (): array => ['closure_key_a' => 'Closure Value A'])
        ->viewData(fn (): array => ['closure_key_b' => 'Closure Value B']);

    expect($component)
        ->getViewData()->toBe([
            'key_a' => 'Value A',
            'closure_key_a' => 'Closure Value A',
            'closure_key_b' => 'Closure Value B',
        ]);
});
