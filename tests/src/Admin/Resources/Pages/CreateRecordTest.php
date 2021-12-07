<?php

use Filament\Tests\Admin\Fixtures\PostResource;
use Filament\Tests\Admin\Resources\TestCase;

uses(TestCase::class);

it('can render page', function () {
    $this->get(PostResource::getUrl('create'))->assertSuccessful();
});
