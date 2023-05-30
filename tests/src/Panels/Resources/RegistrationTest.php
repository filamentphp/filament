<?php

use Filament\Facades\Filament;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Filament\Tests\Panels\Resources\TestCase;

uses(TestCase::class);

it('can register resources', function () {
    expect(Filament::getResources())
        ->toContain(PostResource::class);
});
