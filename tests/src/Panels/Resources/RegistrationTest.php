<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Panels\Resources\TestCase;

uses(TestCase::class);

it('can register resources', function (): void {
    expect(Filament::getResources())
        ->toContain(PostResource::class);
});
