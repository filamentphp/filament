<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('fields can save relationships', function () {
    $numberOfRelationshipsSaved = 0;
    $isFieldVisible = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved) {
        $numberOfRelationshipsSaved++;
    };

    $componentContainer = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->visible(function () use (&$isFieldVisible) {
                    return $isFieldVisible;
                }),
        ])
        ->model(Post::factory()->create());

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $isFieldVisible = false;

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});

test('hidden fields can save relationships when requested', function () {
    $numberOfRelationshipsSaved = 0;
    $shouldSaveRelationships = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved) {
        $numberOfRelationshipsSaved++;
    };

    $componentContainer = ComponentContainer::make(Livewire::make())
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

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $shouldSaveRelationships = false;

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});
