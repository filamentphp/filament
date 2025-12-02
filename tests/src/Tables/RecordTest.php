<?php

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can list records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts);
});

it('can set extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(['data-test' => 'value']);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-test'))->toBe('value');
});

it('can set dynamic extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(fn ($record) => [
        'data-id' => (string) $record->getKey(),
    ]);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-id'))->toBe((string) $post->getKey());
});

it('can merge extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(['data-test' => 'value1']);
    $table->extraRecordLinkAttributes(['data-test2' => 'value2'], merge: true);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-test'))->toBe('value1')
        ->and($attributes->get('data-test2'))->toBe('value2');
});
