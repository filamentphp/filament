<?php

use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Filament\Tests\Panels\Fixtures\Resources\UserResource\Pages\CreateUser;
use Filament\Tests\Panels\Fixtures\Resources\UserResource\Pages\CreateUserWithPersonalPivotData;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can use personal pivot data for multiple select', function () {
    $teams = Team::factory()->count(5)->create();
    $newUser = User::factory()->definition();

    livewire(CreateUserWithPersonalPivotData::class)
        ->fillForm([
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'password' => $newUser['password'],
            'role' => 'admin',
            'team_id' => [
                $teams->get(0)->id,
                $teams->get(2)->id,
                $teams->get(4)->id,
            ],
        ])
        ->call('create');

    $user = User::where('email', $newUser['email'])->first();

    expect($user)->not->toBeNull();

    expect(DB::table('team_user')->where([
        'user_id' => $user->id,
        'team_id' => $teams[0]->id,
        'role' => 'owner',
    ])->exists())->toBeTrue();

    expect(DB::table('team_user')->where([
        'user_id' => $user->id,
        'role' => 'admin',
    ])->pluck('team_id')->toArray())->toBe([3, 5]);
});

it('can use old pivot data for multiple select', function () {
    $teams = Team::factory()->count(5)->create();
    $newUser = User::factory()->definition();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'password' => $newUser['password'],
            'role' => 'admin',
            'team_id' => [
                $teams->get(0)->id,
                $teams->get(2)->id,
                $teams->get(4)->id,
            ],
        ])
        ->call('create');

    $user = User::where('email', $newUser['email'])->first();

    expect($user)->not->toBeNull();

    expect(DB::table('team_user')->where([
        'user_id' => $user->id,
        'role' => 'owner',
    ])->count())->toBe(0);

    expect(DB::table('team_user')->where([
        'user_id' => $user->id,
        'role' => 'admin',
    ])->pluck('team_id')->toArray())->toBe([1, 3, 5]);
});
