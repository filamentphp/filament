<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Pages\TestCase;
use Filament\Tests\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Settings::getUrl())->assertSuccessful();
});

it('can generate a slug based on the page name', function () {
    expect(Settings::getSlug())
        ->toBe('settings');
});
