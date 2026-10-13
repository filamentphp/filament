<?php

use Filament\Support\View\DefaultLoadingIndicator;
use Filament\Tests\TestCase;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

it('renders an SVG and forwards attributes', function (): void {
    $html = (new DefaultLoadingIndicator)->toHtml(
        (new ComponentAttributeBag)->class(['fi-icon', 'fi-custom']),
    );

    expect($html)
        ->toContain('<svg')
        ->toContain('fi-custom');
});
