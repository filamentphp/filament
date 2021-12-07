<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;

uses(TestCase::class);

it('can render page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});
