<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

test('containers can store a record', function (): void {
    $schema = Schema::make(Livewire::make())
        ->model($record = new Post);

    expect($schema)
        ->getRecord()->toBe($record);
});

test('containers can get their model class', function (): void {
    $schema = Schema::make(Livewire::make())
        ->model($model = Post::class);

    expect($schema)
        ->getModel()->toBe($model);

    $schema->model(new Post);

    expect($schema)
        ->getModel()->toBe($model);
});

test('containers can get an instance of their model', function (): void {
    $schema = Schema::make(Livewire::make())
        ->model($modelInstance = new Post);

    expect($schema)
        ->getModelInstance()->toBe($modelInstance);

    $schema->model($model = Post::class);

    expect($schema)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components can store a record', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->model($record = new Post);

    expect($component)
        ->getRecord()->toBe($record);
});

test('components can get their model class', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->model($model = Post::class);

    expect($component)
        ->getModel()->toBe($model);
});

test('components can get an instance of their model', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->model($model = Post::class);

    expect($component)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components can inherit their container record', function (): void {
    $component = (new Component)
        ->container(
            Schema::make(Livewire::make())
                ->model($record = new Post),
        );

    expect($component)
        ->getRecord()->toBe($record);
});

test('components can inherit their container model class', function (): void {
    $component = (new Component)
        ->container(
            Schema::make(Livewire::make())
                ->model($model = Post::class),
        );

    expect($component)
        ->getModel()->toBe($model)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components do not inherit their container record if they have their own model defined', function (): void {
    $component = (new Component)
        ->container(
            Schema::make(Livewire::make())
                ->model(new User),
        )
        ->model($model = Post::class);

    expect($component)
        ->getRecord()->toBeNull()
        ->getModel()->toBe($model)
        ->getModelInstance()->toBeInstanceOf($model);
});
