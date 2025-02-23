<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('belongs to container', function (): void {
    $component = (new Component)
        ->container($schema = Schema::make(Livewire::make()));

    expect($component)
        ->getContainer()->toBe($schema);
});

it('can access container\'s Livewire component', function (): void {
    $component = (new Component)
        ->container(Schema::make($livewire = Livewire::make()));

    expect($component)
        ->getLivewire()->toBe($livewire);
});

it('has child components', function (): void {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($parentComponent = new Component)
        ->container(Schema::make(Livewire::make()))
        ->childComponents($components)
        ->getChildSchema()
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->getParentComponent()->toBe($parentComponent),
        );
});

it('has a label', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->label($label = Str::random());

    expect($component)
        ->getLabel()->toBe($label);
});

it('can have meta', function (): void {
    $component = (new Component)
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

it('can be cloned', function (): void {
    $afterClonedCallbackCalledCount = 0;
    $afterClonedCallbackClone = null;
    $afterClonedCallbackOriginal = null;

    $component = (new Component)
        ->afterCloned(function (Component $clone, Component $original) use (&$afterClonedCallbackCalledCount, &$afterClonedCallbackClone, &$afterClonedCallbackOriginal): void {
            $afterClonedCallbackCalledCount++;
            $afterClonedCallbackClone = $clone;
            $afterClonedCallbackOriginal = $original;
        });

    $clone = $component->getClone();

    expect($afterClonedCallbackCalledCount)
        ->toBe(1);

    expect($afterClonedCallbackClone)
        ->not->toBe($component)
        ->toBe($clone);

    expect($afterClonedCallbackOriginal)
        ->toBe($component);
});
