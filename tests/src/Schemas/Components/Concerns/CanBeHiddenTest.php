<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

describe('basic visibility', function (): void {
    test('components can be hidden', function (): void {
        $component = (new Component)
            ->container(Schema::make(Livewire::make()))
            ->hidden();

        expect($component)
            ->isHidden()->toBeTrue();
    });

    test('components can be hidden based on condition', function (): void {
        $statePath = Str::random();

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->visible(fn (callable $get) => $get($statePath) === false),
            ])
            ->fill([
                $statePath => true,
            ]);

        expect($schema->getComponents())
            ->toHaveCount(0);

        $schema->components([
            (new Component)
                ->whenTruthy($statePath),
        ]);

        expect($schema->getComponents())
            ->toHaveLength(1);

        $schema->components([
            (new Component)
                ->whenFalsy($statePath),
        ]);

        expect($schema->getComponents())
            ->toHaveLength(0);

        $schema
            ->components([
                (new Component)
                    ->whenFalsy([$statePath, 'bob']),
            ])
            ->fill([
                $statePath => true,
                'bob' => true,
            ]);

        expect($schema->getComponents())
            ->toHaveLength(0);

        $schema
            ->components([
                (new Component)
                    ->whenTruthy([$statePath, 'bob']),
            ])
            ->fill([
                $statePath => true,
                'bob' => true,
            ]);

        expect($schema->getComponents())
            ->toHaveLength(1);
    });

    test('hidden components are not returned from container', function (): void {
        $components = [];

        foreach (range(1, $visibleCount = rand(2, 10)) as $index) {
            $components[] = new Component;
        }

        foreach (range(1, rand(2, 10)) as $index) {
            $components[] = (new Component)->hidden();
        }

        $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
            ->components($components)
            ->getComponents();

        expect($componentsBoundToContainer)
            ->toHaveCount($visibleCount)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(Component::class)
                    ->isHidden()->toBeFalse()
                    ->getContainer()->toBe($schema),
            );
    });
});

describe('visibility caching', function (): void {
    test('sections sharing a state path and visibility closure retain independent visibility', function (bool $visibleFirst, string $visibilityMethod): void {
        $hiddenCondition = static function (Component $component): bool {
            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if (count($childSchema->getComponents())) {
                    return false;
                }
            }

            return true;
        };

        $condition = ($visibilityMethod === 'hidden')
            ? $hiddenCondition
            : static fn (Component $component): bool => ! $hiddenCondition($component);

        $hiddenSection = Section::make('Hidden section')
            ->statePath('details')
            ->{$visibilityMethod}($condition)
            ->schema([TextInput::make('first')->hidden()]);

        $visibleSection = Section::make('Visible section')
            ->statePath('details')
            ->{$visibilityMethod}($condition)
            ->schema([TextInput::make('second')]);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components($visibleFirst
                ? [$visibleSection, $hiddenSection]
                : [$hiddenSection, $visibleSection]);

        expect(array_values($schema->getComponents()))->toBe([$visibleSection]);

        expect(Component::withVisibilityCache(fn (): array => array_values($schema->getComponents())))
            ->toBe([$visibleSection]);
    })->with([
        'hidden section evaluated first' => false,
        'visible section evaluated first' => true,
    ])->with(['hidden', 'visible']);

    test('cloned sections retain independent child visibility', function (): void {
        $section = Section::make('Details')
            ->statePath('details')
            ->hiddenWhenAllChildComponentsHidden()
            ->schema([TextInput::make('value')->hidden()]);

        Schema::make(Livewire::make())->statePath('data')
            ->components([$section])->getComponents(withHidden: true);

        Component::withVisibilityCache(function () use ($section): void {
            expect($section->isHidden())->toBeTrue();

            $clone = $section->getClone()->schema([TextInput::make('value')]);

            expect($clone->isHidden())->toBeFalse()
                ->and($section->isHidden())->toBeTrue();
        });
    });

    test('re-registering a visibility condition clears its cached result', function (string $method, bool $reuseCondition): void {
        $component = Fieldset::make('Match');
        $condition = static fn (Fieldset $component): bool => $component->getLabel() === 'Match';
        $registerCondition = static function (Fieldset $component) use ($method, $reuseCondition, $condition): void {
            $component->{$method}($reuseCondition ? $condition : static fn (Fieldset $component): bool => $component->getLabel() === 'Match');
        };
        $registerCondition($component);

        Component::withVisibilityCache(function () use ($component, $registerCondition, $method): void {
            expect($component->isHidden())->toBe($method === 'hidden');

            $component->label('Other');
            $registerCondition($component);

            expect($component->isHidden())->toBe($method === 'visible');
        });
    })->with(['hidden', 'visible'])->with([
        'same closure' => true,
        'closure factory' => false,
    ]);

    test('visibility is memoized across nested cache scopes and refreshed after the outer scope', function (): void {
        $calls = 0;
        $component = Fieldset::make()->hidden(static function () use (&$calls): bool {
            $calls++;

            return false;
        });

        Component::withVisibilityCache(function () use ($component, &$calls): void {
            expect($component->isVisible())->toBeTrue();

            Component::withVisibilityCache(function () use ($component): void {
                expect($component->isVisible())->toBeTrue();
            });

            expect($component->isVisible())->toBeTrue()
                ->and($calls)->toBe(1);
        });

        Component::withVisibilityCache(function () use ($component, &$calls): void {
            expect($component->isVisible())->toBeTrue()
                ->and($calls)->toBe(2);
        });
    });

    test('visibility cache releases discarded components and callbacks after completion', function (bool $throwException): void {
        $componentReference = $conditionReference = null;

        try {
            Component::withVisibilityCache(function () use (&$componentReference, &$conditionReference, $throwException): void {
                $condition = static fn (): bool => $throwException;
                $component = Fieldset::make()->visible($condition);
                expect($component->isVisible())->toBe($throwException);
                $componentReference = WeakReference::create($component);
                $conditionReference = WeakReference::create($condition);
                $component->visible(false);

                if ($throwException) {
                    throw new RuntimeException('Visibility evaluation failed');
                }
            });
        } catch (RuntimeException $exception) {
            expect($exception->getMessage())->toBe('Visibility evaluation failed');
        }

        gc_collect_cycles();

        expect(Component::isVisibilityCacheEnabled())->toBeFalse()
            ->and($componentReference->get())->toBeNull()
            ->and($conditionReference->get())->toBeNull();
    })->with(['normal completion' => false, 'exception' => true]);

    test('rebuilt keyless components retain independent visibility', function (): void {
        $schema = Schema::make(Livewire::make());
        $condition = static fn (Fieldset $component): bool => $component->getLabel() === 'Shown';

        Component::withVisibilityCache(function () use ($schema, $condition): void {
            foreach ([false, true, true, false, false, false, true, true, true, true, false] as $isVisible) {
                $schema->components([]);
                gc_collect_cycles();
                $schema->components([
                    Fieldset::make($isVisible ? 'Shown' : 'Hidden')->visible($condition),
                ]);

                expect($schema->getComponents(withHidden: true)[0]->getKey())->toBeNull()
                    ->and($schema->getComponents())->toHaveCount((int) $isVisible);
            }
        });
    });
});

describe('`hiddenOn()` and `visibleOn()`', function (): void {
    test('components can be hidden based on `Livewire` component', function (): void {
        $components = Schema::make(Foo::make())
            ->components([
                TextInput::make('foo')
                    ->hiddenOn(Foo::class),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0);

        $components = Schema::make(Bar::make())
            ->components([
                TextInput::make('foo')
                    ->hiddenOn(Foo::class),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(TextInput::class)
                    ->isHidden()->toBeFalse()
            );

        $components = Schema::make(Bar::make())
            ->components([
                TextInput::make('foo')
                    ->hiddenOn([Foo::class, Bar::class]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0);
    });

    test('components can be hidden based on an `Operation` enum case', function (): void {
        $components = Schema::make(Foo::make())
            ->operation('create')
            ->components([
                TextInput::make('foo')
                    ->hiddenOn(Operation::Create),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0);

        $components = Schema::make(Bar::make())
            ->operation('edit')
            ->components([
                TextInput::make('foo')
                    ->hiddenOn(Operation::Create),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(TextInput::class)
                    ->isHidden()->toBeFalse()
            );

        $components = Schema::make(Bar::make())
            ->operation('edit')
            ->components([
                TextInput::make('foo')
                    ->hiddenOn([Operation::Create, Operation::Edit]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0);

        $components = Schema::make(Bar::make())
            ->operation('view')
            ->components([
                TextInput::make('foo')
                    ->hiddenOn([Operation::Create, Operation::Edit]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1);
    });

    test('components can be visible based on `Livewire` component', function (): void {
        $components = Schema::make(Foo::make())
            ->components([
                TextInput::make('foo')
                    ->visibleOn(Foo::class),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1);

        $components = Schema::make(Bar::make())
            ->components([
                TextInput::make('foo')
                    ->visibleOn(Foo::class),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(TextInput::class)
                    ->isHidden()->toBeFalse()
            );

        $components = Schema::make(Bar::make())
            ->components([
                TextInput::make('foo')
                    ->visibleOn([Foo::class, Bar::class]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1);
    });

    test('components can be visible based on an `Operation` enum case', function (): void {
        $components = Schema::make(Foo::make())
            ->operation('create')
            ->components([
                TextInput::make('foo')
                    ->visibleOn(Operation::Create),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1);

        $components = Schema::make(Bar::make())
            ->operation('create')
            ->components([
                TextInput::make('foo')
                    ->visibleOn(Operation::Edit),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(TextInput::class)
                    ->isHidden()->toBeFalse()
            );

        $components = Schema::make(Bar::make())
            ->operation('create')
            ->components([
                TextInput::make('foo')
                    ->visibleOn([Operation::Create, Operation::Edit]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(1);

        $components = Schema::make(Bar::make())
            ->operation('view')
            ->components([
                TextInput::make('foo')
                    ->visibleOn([Operation::Create, Operation::Edit]),
            ])
            ->getComponents();

        expect($components)
            ->toHaveLength(0);
    });
});

class Foo extends Livewire {}
class Bar extends Livewire {}
