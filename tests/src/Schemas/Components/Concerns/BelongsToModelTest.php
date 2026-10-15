<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can store a record', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->model($record = new Post);

    expect($component->getRecord())->toBe($record);
});

it('can get the model class', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->model(Post::class);

    expect($component->getModel())->toBe(Post::class);
});

it('can get an instance of the model', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->model(Post::class);

    expect($component->getModelInstance())->toBeInstanceOf(Post::class);
});

describe('container inheritance', function (): void {
    it('can inherit its container record', function (): void {
        $component = (new Component)->container(Schema::make(Livewire::make())->model($record = new Post));

        expect($component->getRecord())->toBe($record);
    });

    it('can inherit its container model class', function (): void {
        $component = (new Component)->container(Schema::make(Livewire::make())->model(Post::class));

        expect($component->getModel())->toBe(Post::class)
            ->and($component->getModelInstance())->toBeInstanceOf(Post::class);
    });

    it('does not inherit its container record if it has its own model', function (): void {
        $component = (new Component)->container(Schema::make(Livewire::make())->model(new User))->model(Post::class);

        expect($component->getRecord())->toBeNull()
            ->and($component->getModel())->toBe(Post::class)
            ->and($component->getModelInstance())->toBeInstanceOf(Post::class);
    });
});
