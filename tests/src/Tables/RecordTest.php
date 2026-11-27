<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Filament\Tests\Tables\Fixtures\HiddenPivotTeamsTable;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can list records', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts);
});

it('keeps hidden pivot values readable in PHP but omits flattened copies from serialized table records', function (bool $duplicates): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['created_at' => '2020-01-01 00:00:00']);
    $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);
    $component = livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'duplicates' => $duplicates]);
    $key = $duplicates ? $user->teams()->withPivot('id')->first()->pivot->id : $team->getKey();

    foreach ([$component->instance()->getTableRecords()->first(), $component->instance()->getTableRecord((string) $key)] as $record) {
        expect($record->created_at)->not->toBeNull()
            ->and($record->user_id)->toBe($user->getKey())
            ->and($record->attributesToArray())->not->toHaveKeys(['created_at', 'pivot_created_at', 'user_id', 'pivot_user_id'])
            ->and($record->attributesToArray()['role'])->toBe('editor');
    }
})->with([false, true]);
