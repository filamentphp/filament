<?php

use Filament\Forms\Components\Field;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('fields can save relationships', function (): void {
    $numberOfRelationshipsSaved = 0;
    $isFieldVisible = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->visible(function () use (&$isFieldVisible) {
                    return $isFieldVisible;
                }),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $isFieldVisible = false;

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});

test('hidden fields can save relationships when requested', function (): void {
    $numberOfRelationshipsSaved = 0;
    $shouldSaveRelationships = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->hidden()
                ->saveRelationshipsWhenHidden(function () use (&$shouldSaveRelationships) {
                    return $shouldSaveRelationships;
                }),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $shouldSaveRelationships = false;

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});

test('fields with `saved(false)` do not save relationships', function (): void {
    $numberOfRelationshipsSaved = 0;
    $isSaved = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->saved(function () use (&$isSaved) {
                    return $isSaved;
                }),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $isSaved = false;

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});

test('`saved(false)` prevents relationship saving regardless of `dehydrated()` setting', function (): void {
    $numberOfRelationshipsSaved = 0;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->dehydrated(true)
                ->saved(false),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(0);
});

test('disabled fields do not save relationships', function (): void {
    $numberOfRelationshipsSaved = 0;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->disabled(),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(0);
});

test('disabled fields can save relationships when saveRelationshipsWhenDisabled is called', function (): void {
    $numberOfRelationshipsSaved = 0;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
        $numberOfRelationshipsSaved++;
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->disabled()
                ->saved()
                ->saveRelationshipsWhenDisabled(),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);
});
