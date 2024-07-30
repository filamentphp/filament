<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('container has state path', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath($containerStatePath = Str::random());

    expect($container)
        ->getStatePath()->toBe($containerStatePath);
});

test('container has state path and inherits state path from parent component', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->parentComponent(
            (new Component)
                ->container(ComponentContainer::make(Livewire::make()))
                ->statePath($parentComponentStatePath = Str::random()),
        )
        ->statePath($containerStatePath = Str::random());

    expect($container)
        ->getStatePath()->toBe("{$parentComponentStatePath}.{$containerStatePath}");
});

test('component has state path', function () {
    $component = (new Component)
        ->container(ComponentContainer::make(Livewire::make()))
        ->statePath($componentStatePath = Str::random());

    expect($component)
        ->getStatePath()->toBe($componentStatePath);
});

test('component inherits state path from container', function () {
    $component = (new Component)
        ->container(
            ComponentContainer::make(Livewire::make())
                ->statePath($containerStatePath = Str::random()),
        );

    expect($component)
        ->getStatePath()->toBe($containerStatePath);
});

test('component has state path and inherits state path from container', function () {
    $component = (new Component)
        ->container(
            ComponentContainer::make(Livewire::make())
                ->statePath($containerStatePath = Str::random()),
        )
        ->statePath($componentStatePath = Str::random());

    expect($component)
        ->getStatePath()->toBe("{$containerStatePath}.{$componentStatePath}");
});

test('state can be hydrated from array', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random()),
        ])
        ->fill([$statePath => ($state = Str::random())]);

    expect($livewire)
        ->getData()->toBe([$statePath => $state]);
});

test('hydrating array state can overwrite existing state', function () {
    $statePath = Str::random();

    ComponentContainer::make(
        $livewire = Livewire::make()
            ->data([
                $statePath => Str::random(),
            ]),
    )
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath),
        ])
        ->fill([]);

    expect($livewire)
        ->getData()->toBe([$statePath => null]);
});

test('state can be hydrated from defaults', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$statePath => $state]);
});

test('hydrating default state can overwrite existing state', function () {
    $statePath = Str::random();

    ComponentContainer::make(
        $livewire = Livewire::make()
            ->data([
                $statePath => Str::random(),
            ]),
    )
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$statePath => null]);
});

test('child component state is not lost by hydrating parent component', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random())
                        ->default($state = Str::random()),
                ]),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$parentStatePath => [$statePath => $state]]);
});

test('child component state is not lost by hydrating parent component defaults', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random())
                        ->default($state = Str::random()),
                ])
                ->default([]),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$parentStatePath => [$statePath => $state]]);
});

test('child component state can be hydrated by parent component defaults', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random()),
                ])
                ->default([$statePath => ($state = Str::random())]),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$parentStatePath => [$statePath => $state]]);
});

test('child component defaults are overwritten by parent component defaults', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random())
                        ->default(Str::random()),
                ])
                ->default([$statePath => ($state = Str::random())]),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$parentStatePath => [$statePath => $state]]);
});

test('missing child component state can be filled with null', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)->statePath($statePath = Str::random()),
                ])
                ->afterStateHydrated(fn (Component $component) => $component->state([])),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$parentStatePath => [$statePath => null]]);
});

test('missing hidden child component state can be filled with null', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->hidden()
                ->schema([
                    (new Component)->statePath($statePath = Str::random()),
                ]),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$statePath => null]);
});

test('custom logic can be executed after state is hydrated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->afterStateHydrated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill([$statePath => ($value = Str::random())]);

    expect($livewire)
        ->getData()->toBe([$statePath => strrev($value)]);
});

test('custom logic can be executed after state is updated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->afterStateUpdated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill([$statePath => ($state = Str::random())])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}"));

    expect($livewire)
        ->getData()->toBe([$statePath => strrev($state)]);
});

test('custom logic can be executed after nested state is updated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->afterStateUpdated(fn (Component $component, $state) => $component->state([strrev($state[0])])),
        ])
        ->fill([$statePath => [$state = Str::random()]])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.0"));

    expect($livewire)
        ->getData()->toBe([$statePath => [strrev($state)]]);
});

test('custom logic can be executed after child component state is updated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->childComponents([
                    (new Component)
                        ->statePath($childComponentStatePath = Str::random())
                        ->afterStateUpdated(fn (Component $component, $state) => $component->state(strrev($state))),
                ]),
        ])
        ->fill([$statePath => [$childComponentStatePath => $state = Str::random()]])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.{$childComponentStatePath}"));

    expect($livewire)
        ->getData()->toBe([$statePath => [$childComponentStatePath => strrev($state)]]);
});

test('custom logic can be executed only once after nested state is updated', function () {
    $calls = 0;

    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->afterStateUpdated(function () use (&$calls) {
                    $calls++;
                }),
        ])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.0"))
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.1"));

    expect($calls)->toEqual(1);
});

test('custom logic can be executed more than once after nested state is updated if the state changes', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->afterStateUpdated(fn (Component $component, $state) => $component->state([$state[0] + 1])),
        ])
        ->fill([$statePath => [0]])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.0"))
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.1"));

    expect($livewire)
        ->getData()->toBe([$statePath => [2]]);
});

test('custom logic on parent component can be executed after child component state is updated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->childComponents([
                    (new Component)
                        ->statePath($childComponentStatePath = Str::random()),
                ])
                ->afterStateUpdated(fn (Component $component, $state) => $component->state([
                    $childComponentStatePath => strrev($state[$childComponentStatePath]),
                ])),
        ])
        ->fill([$statePath => [$childComponentStatePath => $state = Str::random()]])
        ->tap(fn (ComponentContainer $container) => $container->callAfterStateUpdated("data.{$statePath}.{$childComponentStatePath}"));

    expect($livewire)
        ->getData()->toBe([$statePath => [$childComponentStatePath => strrev($state)]]);
});

test('state can be dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => $state],
        ]);
});

test('state can be dehydrated using custom logic', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->dehydrateStateUsing(fn ($state) => strrev($state)),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

test('custom logic can be executed before state is dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->beforeStateDehydrated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill();

    $container->callBeforeStateDehydrated();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

test('components can be excluded from state dehydration', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath(Str::random())
                ->default(Str::random())
                ->dehydrated(false),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('components can be excluded from state dehydration if their parent component is', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->dehydrated(false)
                ->schema([
                    (new Component)
                        ->statePath(Str::random())
                        ->default(Str::random()),
                ]),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('hidden components are excluded from state dehydration', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath(Str::random())
                ->default(Str::random())
                ->hidden(),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('hidden components are excluded from state dehydration if their parent component is', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->hidden()
                ->schema([
                    (new Component)
                        ->statePath(Str::random())
                        ->default(Str::random()),
                ]),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('hidden components are excluded from state dehydration except if they are marked as dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath(Str::random())
                ->default(Str::random())
                ->hidden()
                ->dehydratedWhenHidden(),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->not()->toBe([]);
});

test('disabled components are excluded from state dehydration', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath(Str::random())
                ->default(Str::random())
                ->disabled(),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('disabled components are excluded from state dehydration if their parent component is', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->disabled()
                ->schema([
                    (new Component)
                        ->statePath(Str::random())
                        ->default(Str::random()),
                ]),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('disabled components are excluded from state dehydration except if they are marked as dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath(Str::random())
                ->default(Str::random())
                ->disabled()
                ->dehydrated(),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->not()->toBe([]);
});

test('disabled components are excluded from state dehydration if their parent component is disabled and not marked as dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->disabled()
                ->schema([
                    (new Component)
                        ->statePath(Str::random())
                        ->default(Str::random())
                        ->dehydrated(),
                ]),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('dehydrated state can be mutated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->mutateDehydratedStateUsing(fn ($state) => strrev($state)),
        ])
        ->fill();

    $containerState = $container->dehydrateState();

    expect($container->mutateDehydratedState($containerState))
        ->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

test('sibling state can be retrieved relatively from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
            $placeholder = Placeholder::make(Str::random())
                ->content(fn (Get $get): string => $get($statePath)),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});

test('sibling nested state can be retrieved relatively from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random())
                        ->default($state = Str::random()),
                ]),
            $placeholder = Placeholder::make(Str::random())
                ->content(fn (Get $get): string => $get("{$parentStatePath}.{$statePath}")),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});

test('parent sibling state can be retrieved relatively from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
            (new Component)
                ->statePath(Str::random())
                ->schema([
                    $placeholder = Placeholder::make(Str::random())
                        ->content(fn (Get $get): string => $get("../{$statePath}")),
                ]),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});

test('sibling state can be retrieved absolutely from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
            $placeholder = Placeholder::make(Str::random())
                ->content(fn (Get $get): string => $get("data.{$statePath}", isAbsolute: true)),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});

test('sibling nested state can be retrieved absolutely from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($parentStatePath = Str::random())
                ->schema([
                    (new Component)
                        ->statePath($statePath = Str::random())
                        ->default($state = Str::random()),
                ]),
            $placeholder = Placeholder::make(Str::random())
                ->content(fn (Get $get): string => $get("data.{$parentStatePath}.{$statePath}", isAbsolute: true)),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});

test('parent sibling state can be retrieved absolutely from another component', function () {
    ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
            (new Component)
                ->statePath(Str::random())
                ->schema([
                    $placeholder = Placeholder::make(Str::random())
                        ->content(fn (Get $get): string => $get("data.{$statePath}", isAbsolute: true)),
                ]),
        ])
        ->fill();

    expect($placeholder)
        ->getContent()->toBe($state);
});
