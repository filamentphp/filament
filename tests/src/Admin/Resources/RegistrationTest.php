<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\PostResource;
use Filament\Tests\Admin\Resources\TestCase;

uses(TestCase::class);

it('can register resources', function () {
    Filament::registerResources([
        PostResource::class,
    ]);

    expect(Filament::getResources())->toContain(PostResource::class);
});
