<?php

use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Configuration\TestCase;

uses(TestCase::class);

it('returns a URL string from `get()`', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => 'John Doe']);

    $url = $provider->get($user);

    expect($url)->toBeString();
    expect($url)->toStartWith('https://ui-avatars.com/api/');
});

it('includes encoded name in the URL', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => 'Jane Smith']);

    $url = $provider->get($user);

    expect($url)->toContain('name=');
});

it('includes white text color in the URL', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => 'Test']);

    $url = $provider->get($user);

    expect($url)->toContain('color=FFFFFF');
});

it('includes background color in the URL', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => 'Test']);

    $url = $provider->get($user);

    expect($url)->toContain('background=');
    expect($url)->toMatch('/background=%23([0-9a-f]{3}){1,2}$/');
});
