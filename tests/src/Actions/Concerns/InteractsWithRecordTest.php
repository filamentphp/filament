<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

uses(TestCase::class);

it('resolves a query from `resolveRecordUsing()` and clears its cached record when reconfigured', function (string $component): void {
    $first = Post::factory()->create(['title' => 'First', 'rating' => 2]);
    $second = Post::factory()->create(['title' => 'Second', 'rating' => 3]);
    $action = ($component === 'action' ? Action::make('inspect') : ActionGroup::make([]))
        ->record((string) $first->getKey())
        ->resolveRecordUsing(static fn (string $key): Builder => Post::query()->whereKey($key)->where('rating', '<', 5));

    expect($action->getRecord()?->getKey())->toBe($first->getKey());
    $first->update(['title' => 'Changed']);
    expect($action->getRecord()?->title)->toBe('First');

    $action->record((string) $second->getKey());
    expect($action->getRecord()?->title)->toBe('Second');

    $action->resolveRecordUsing(static fn (string $key): Builder => Post::query()->whereKey($key)->where('rating', '>', 5));
    expect($action->getRecord())->toBeNull();
})->with(['action', 'group']);

it('keeps model-returning `resolveRecordUsing()` callbacks compatible', function (): void {
    $post = Post::factory()->create(['title' => 'Original']);
    $action = Action::make('inspect')->record((string) $post->getKey())
        ->resolveRecordUsing(static fn (string $key): ?Post => Post::find($key));

    expect($action->getRecord()?->getKey())->toBe($post->getKey());
    $post->update(['title' => 'Changed']);
    expect($action->getRecord()?->title)->toBe('Original');
});
