<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('belongs to container', function () {
    $component = (new Component())
        ->container($container = ComponentContainer::make(Livewire::make()));

    expect($component)
        ->getContainer()->toBe($container);
});

it('can access container\'s Livewire component', function () {
    $component = (new Component())
        ->container(ComponentContainer::make($livewire = Livewire::make()));

    expect($component)
        ->getLivewire()->toBe($livewire);
});

it('has child components', function () {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component();
    }

    $componentsBoundToContainer = ($parentComponent = new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->childComponents($components)
        ->getChildComponentContainer()
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->getParentComponent()->toBe($parentComponent),
        );
});

it('has a label', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->label($label = Str::random());

    expect($component)
        ->getLabel()->toBe($label);
});

it('can have meta', function () {
    $component = (new Component())
        ->meta('foo', 'bar')
        ->meta('bob', 'baz');

    expect($component)
        ->hasMeta('foo')->toBeTrue()
        ->getMeta('foo')->toBe('bar')
        ->getMeta(['foo', 'bob'])->toEqual([
            'foo' => 'bar',
            'bob' => 'baz',
        ]);
});
