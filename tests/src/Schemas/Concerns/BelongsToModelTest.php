<?php

use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can store a record', function (): void {
    $schema = Schema::make(Livewire::make())->model($record = new Post);

    expect($schema->getRecord())->toBe($record);
});

it('can get the model class from a class name or record', function (): void {
    $schema = Schema::make(Livewire::make())->model(Post::class);

    expect($schema->getModel())->toBe(Post::class);

    $schema->model(new Post);

    expect($schema->getModel())->toBe(Post::class);
});

it('can get an instance of the model', function (): void {
    $schema = Schema::make(Livewire::make())->model($record = new Post);

    expect($schema->getModelInstance())->toBe($record);

    $schema->model(Post::class);

    expect($schema->getModelInstance())->toBeInstanceOf(Post::class);
});
