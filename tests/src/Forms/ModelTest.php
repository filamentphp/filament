<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

test('containers can store a record', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->model($record = new Post());

    expect($container)
        ->getRecord()->toBe($record);
});

test('containers can get their model class', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->model($model = Post::class);

    expect($container)
        ->getModel()->toBe($model);

    $container->model(new Post());

    expect($container)
        ->getModel()->toBe($model);
});

test('containers can get an instance of their model', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->model($modelInstance = new Post());

    expect($container)
        ->getModelInstance()->toBe($modelInstance);

    $container->model($model = Post::class);

    expect($container)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components can store a record', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->model($record = new Post());

    expect($component)
        ->getRecord()->toBe($record);
});

test('components can get their model class', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->model($model = Post::class);

    expect($component)
        ->getModel()->toBe($model);
});

test('components can get an instance of their model', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->model($model = Post::class);

    expect($component)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components can inherit their container record', function () {
    $component = (new Component())
        ->container(
            ComponentContainer::make(Livewire::make())
                ->model($record = new Post()),
        );

    expect($component)
        ->getRecord()->toBe($record);
});

test('components can inherit their container model class', function () {
    $component = (new Component())
        ->container(
            ComponentContainer::make(Livewire::make())
                ->model($model = Post::class),
        );

    expect($component)
        ->getModel()->toBe($model)
        ->getModelInstance()->toBeInstanceOf($model);
});

test('components do not inherit their container record if they have their own model defined', function () {
    $component = (new Component())
        ->container(
            ComponentContainer::make(Livewire::make())
                ->model(new User()),
        )
        ->model($model = Post::class);

    expect($component)
        ->getRecord()->toBeNull()
        ->getModel()->toBe($model)
        ->getModelInstance()->toBeInstanceOf($model);
});
