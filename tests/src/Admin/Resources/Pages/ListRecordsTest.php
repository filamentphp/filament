<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;
use Filament\Tests\Admin\Resources\TestCase;

uses(TestCase::class);

it('can render posts page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});

it('can render users page', function () {
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
});
