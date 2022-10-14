<?php

use Filament\Facades\Filament;
use Filament\Tests\App\Fixtures\Resources\PostResource;
use Filament\Tests\App\Resources\TestCase;

uses(TestCase::class);

it('can register resources', function () {
    expect(Filament::getResources())
        ->toContain(PostResource::class);
});
