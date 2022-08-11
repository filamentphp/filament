<?php

use Filament\Tables\Actions\DeleteAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    $this->assertModelMissing($post);
});

it('can call an action with data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => null,
        ])
        ->assertHasTableActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PostsTable::class)
        ->mountTableAction('data')
        ->assertTableActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call an action with arguments', function () {
    livewire(PostsTable::class)
        ->callTableAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and hold', function () {
    livewire(PostsTable::class)
        ->callTableAction('hold')
        ->assertEmitted('hold-called')
        ->assertTableActionHeld('hold');
});

it('can hide an action', function () {
    livewire(PostsTable::class)
        ->assertTableActionVisible('visible')
        ->assertTableActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(PostsTable::class)
        ->assertTableActionEnabled('enabled')
        ->assertTableActionDisabled('disabled');
});
