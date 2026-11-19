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

    test('components sharing a key are hidden independently when all their child components are hidden', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Section::make('First')
                    ->statePath('details')
                    ->hiddenWhenAllChildComponentsHidden()
                    ->schema([
                        TextInput::make('first')->hidden(),
                    ]),
                Section::make('Second')
                    ->statePath('details')
                    ->hiddenWhenAllChildComponentsHidden()
                    ->schema([
                        TextInput::make('second'),
                    ]),
            ]);

        $components = Component::withVisibilityCache(fn (): array => $schema->getComponents());

        expect($components)
            ->toHaveCount(1)
            ->sequence(
                fn ($component) => $component->getHeading()->toBe('Second'),
            );
    });

    test('components sharing a key and a condition closure are hidden independently', function (): void {
        $isHidden = static fn (Section $component): bool => $component->getHeading() === 'First';

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Section::make('First')
                    ->statePath('details')
                    ->hidden($isHidden),
                Section::make('Second')
                    ->statePath('details')
                    ->hidden($isHidden),
            ]);

        $components = Component::withVisibilityCache(fn (): array => $schema->getComponents());

        expect($components)
            ->toHaveCount(1)
            ->sequence(
                fn ($component) => $component->getHeading()->toBe('Second'),
            );
    });

    test('components without a key do not reuse the cached visibility of a freed component', function (): void {
        $isHidden = static fn (): Closure => static fn (Fieldset $component): bool => $component->getLabel() === 'Hidden';

        $isFieldsetHidden = function (string $label) use ($isHidden): bool {
            $fieldset = Fieldset::make($label)->hidden($isHidden());

            Schema::make(Livewire::make())
                ->components([$fieldset])
                ->getComponents(withHidden: true);

            return $fieldset->isHidden();
        };

        Component::withVisibilityCache(function () use ($isFieldsetHidden): void {
            foreach (range(1, 50) as $attempt) {
                expect($isFieldsetHidden('Hidden'))->toBeTrue();

                gc_collect_cycles();

                expect($isFieldsetHidden('Visible'))->toBeFalse();

                gc_collect_cycles();
            }
        });
    });

    test('cloned components share the cached visibility of the original', function (): void {
        $evaluations = 0;

        $section = Section::make('Section')
            ->hidden(function () use (&$evaluations): bool {
                $evaluations++;

                return false;
            });

        Schema::make(Livewire::make())
            ->components([$section])
            ->getComponents(withHidden: true);

        Component::withVisibilityCache(function () use ($section): void {
            expect($section->isHidden())->toBeFalse();
            expect($section->getClone()->isHidden())->toBeFalse();
        });

        expect($evaluations)->toBe(1);
    });

    test('components sharing a key and a `visible()` closure are shown independently', function (): void {
        $isVisible = static fn (Section $component): bool => $component->getHeading() === 'Second';

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Section::make('First')
                    ->statePath('details')
                    ->visible($isVisible),
                Section::make('Second')
                    ->statePath('details')
                    ->visible($isVisible),
            ]);

        $components = Component::withVisibilityCache(fn (): array => $schema->getComponents());

        expect($components)
            ->toHaveCount(1)
            ->sequence(
                fn ($component) => $component->getHeading()->toBe('Second'),
            );
    });

    test('conditions registered again inside an active visibility cache are evaluated again', function (): void {
        $section = Section::make('Section');

        Schema::make(Livewire::make())
            ->components([$section])
            ->getComponents(withHidden: true);

        Component::withVisibilityCache(function () use ($section): void {
            $section->hidden(fn (): bool => true);
            expect($section->isHidden())->toBeTrue();

            $section->hidden(fn (): bool => false);
            expect($section->isHidden())->toBeFalse();

            $section->visible(fn (): bool => false);
            expect($section->isHidden())->toBeTrue();

            $section->visible(fn (): bool => true);
            expect($section->isHidden())->toBeFalse();
        });
    });

    test('a subclass replacing the condition closure directly is evaluated again', function (): void {
        $component = new class extends Component
        {
            public function replaceHiddenCondition(Closure $condition): void
            {
                $this->isHidden = $condition;
            }
        };

        $component->key('custom')->hidden(fn (): bool => true);

        Schema::make(Livewire::make())
            ->components([$component])
            ->getComponents(withHidden: true);

        $isNotHidden = fn (): bool => false;

        Component::withVisibilityCache(function () use ($component, $isNotHidden): void {
            expect($component->isHidden())->toBeTrue();

            $component->replaceHiddenCondition($isNotHidden);

            expect($component->isHidden())->toBeFalse();
        });
    });

    test('a clone with a new condition is cached separately from the original', function (): void {
        $section = Section::make('Section')
            ->hidden(fn (): bool => false);

        Schema::make(Livewire::make())
            ->components([$section])
            ->getComponents(withHidden: true);

        Component::withVisibilityCache(function () use ($section): void {
            expect($section->isHidden())->toBeFalse();

            $clone = $section->getClone()->hidden(fn (): bool => true);

            expect($clone->isHidden())->toBeTrue();
            expect($section->isHidden())->toBeFalse();
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
