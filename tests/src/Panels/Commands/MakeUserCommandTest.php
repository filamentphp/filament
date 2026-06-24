<?php

use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Facades\Filament;
use Filament\PanelRegistry;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

uses(TestCase::class)->group('commands');

$removeDefaultPanel = function (): void {
    $registry = app(PanelRegistry::class);

    foreach ($registry->all() as $panel) {
        invade($panel)->isDefault = false;
    }

    $registry->defaultPanel = null;

    expect(fn (): mixed => Filament::getDefaultPanel())
        ->toThrow(NoDefaultPanelSetException::class);
};

it('can create a user with all the details passed as options', function (): void {
    $this->artisan('make:filament-user', [
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $user = User::query()->where('email', 'dan@filamentphp.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Dan Harrin');
});

it('hashes the user password', function (): void {
    $this->artisan('make:filament-user', [
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $user = User::query()->where('email', 'dan@filamentphp.com')->first();

    expect($user->password)->not->toBe('password')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('prompts for the user details when they are not passed as options', function (): void {
    $this->artisan('make:filament-user', [
        '--panel' => 'admin',
    ])
        ->expectsQuestion('Name', 'Dan Harrin')
        ->expectsQuestion('Email address', 'dan@filamentphp.com')
        ->expectsQuestion('Password', 'password')
        ->assertSuccessful();

    $user = User::query()->where('email', 'dan@filamentphp.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Dan Harrin')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('prompts to select the panel when `--panel` is not passed', function (): void {
    $this->artisan('make:filament-user', [
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
    ])
        ->expectsQuestion('Which panel would you like to create this user in?', 'admin')
        ->assertSuccessful();

    expect(User::query()->where('email', 'dan@filamentphp.com')->exists())
        ->toBeTrue();
});

it('includes the panel login URL in the success message', function (): void {
    $loginUrl = Filament::getPanel('admin')->getLoginUrl();

    $this->artisan('make:filament-user', [
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain($loginUrl)
        ->assertSuccessful();
});

it('fails when Filament has not been installed', function (): void {
    app(PanelRegistry::class)->panels = [];

    $this->artisan('make:filament-user', [
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain('Filament has not been installed yet')
        ->assertFailed();

    expect(User::query()->count())->toBe(0);
});

it('can create a user in the panel given by `--panel` when no default panel is set', function () use ($removeDefaultPanel): void {
    $removeDefaultPanel();

    $this->artisan('make:filament-user', [
        '--panel' => 'admin',
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
        '--no-interaction' => true,
    ])->assertSuccessful();

    expect(User::query()->where('email', 'dan@filamentphp.com')->exists())
        ->toBeTrue();
});

it('can prompt for the panel when no default panel is set', function () use ($removeDefaultPanel): void {
    $removeDefaultPanel();

    $this->artisan('make:filament-user', [
        '--name' => 'Dan Harrin',
        '--email' => 'dan@filamentphp.com',
        '--password' => 'password',
    ])
        ->expectsQuestion('Which panel would you like to create this user in?', 'admin')
        ->assertSuccessful();

    expect(User::query()->where('email', 'dan@filamentphp.com')->exists())
        ->toBeTrue();
});
