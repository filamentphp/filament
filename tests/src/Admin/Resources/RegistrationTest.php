<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;

uses(TestCase::class);

it('can register resources', function () {
    expect(Filament::getResources())
        ->toContain(PostResource::class);
});
