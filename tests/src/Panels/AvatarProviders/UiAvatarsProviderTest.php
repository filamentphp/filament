<?php

use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Configuration\TestCase;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Eloquent\Model;

uses(TestCase::class);

it('returns a URL string from `get()`', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => 'John Doe']);

    $url = $provider->get($user);

    expect($url)->toBeString();
    expect($url)->toStartWith('https://ui-avatars.com/api/');
});

it('generates default avatars for `Model` and non-Eloquent `Authenticatable` records', function (bool $isEloquent): void {
    $record = $isEloquent
        ? new class extends Model implements HasAvatar, HasName
        {
            public function getFilamentAvatarUrl(): ?string
            {
                return null;
            }

            public function getFilamentName(): string
            {
                return 'Taylor Morgan';
            }
        }
    : new class([]) extends GenericUser implements HasAvatar, HasName
    {
        public function getFilamentAvatarUrl(): ?string
        {
            return null;
        }

        public function getFilamentName(): string
        {
            return 'Jordan Ellis';
        }
    };

    $url = Filament::getUserAvatarUrl($record);

    expect($url)->toStartWith('https://ui-avatars.com/api/');
    expect($url)->toContain($isEloquent ? 'name=T+M' : 'name=J+E');
})->with([
    'Eloquent model' => [true],
    'non-Eloquent user' => [false],
]);

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

it('skips leading punctuation when building initials', function (): void {
    $provider = new UiAvatarsProvider;
    $user = User::factory()->create(['name' => '[SYSTEM] Admin']);

    $url = $provider->get($user);

    expect($url)->toContain('name=S+A');
    expect($url)->not->toContain('name=%5B');
});
