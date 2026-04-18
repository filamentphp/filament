<?php

use Filament\Support\Contracts\LoadingIndicator;
use Filament\Support\View\DefaultLoadingIndicator;
use Filament\Tests\TestCase;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

it('binds the `LoadingIndicator` contract to `DefaultLoadingIndicator` by default', function (): void {
    expect(app(LoadingIndicator::class))
        ->toBeInstanceOf(DefaultLoadingIndicator::class);
});

it('renders the `DefaultLoadingIndicator` as an SVG and forwards attributes', function (): void {
    $html = (new DefaultLoadingIndicator)->toHtml(
        (new ComponentAttributeBag)->class(['fi-icon', 'fi-custom']),
    );

    expect($html)
        ->toContain('<svg')
        ->toContain('fi-custom');
});

it('allows swapping the loading indicator implementation via the container', function (): void {
    app()->bind(LoadingIndicator::class, CustomLoadingIndicator::class);

    expect(app(LoadingIndicator::class))
        ->toBeInstanceOf(CustomLoadingIndicator::class);

    expect(app(LoadingIndicator::class)->toHtml(new ComponentAttributeBag))
        ->toBe('<div>Custom</div>');
});

class CustomLoadingIndicator implements LoadingIndicator
{
    public function toHtml(ComponentAttributeBag $attributes): string
    {
        return '<div>Custom</div>';
    }
}
