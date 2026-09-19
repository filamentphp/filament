<?php

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

describe('state retrieval with `Get`', function (): void {
    test('sibling state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('sibling nested state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('parent sibling state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('sibling state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('sibling nested state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('parent sibling state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
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

    test('cast state can be retrieved when queried by the containing component itself for its own descendant', function (): void {
        // Mirrors `Tab::badge()`/`visible()` reading a field in that same tab's
        // own `schema()`: the container itself (no `statePath()` of its own,
        // like `Tab`) is the one calling `$get()`, which excludes its own
        // children from the normal search to prevent infinite loops when
        // computing dynamic child schemas. The target field here is a normal,
        // already-defined descendant, so its cast should still be applied
        // rather than falling back to raw state.
        $container = (new Component)
            ->schema([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default(GetTestStatus::Active->value)
                    ->stateCast(new EnumStateCast(GetTestStatus::class)),
            ]);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$container])
            ->fill();

        $get = new Get($container);

        expect($get($statePath))->toBe(GetTestStatus::Active);
    });

    test('cast state is retrieved identically whether queried by the field\'s own container or a sibling container', function (): void {
        $sibling = (new Component)->schema([]);

        $container = (new Component)
            ->schema([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default(GetTestStatus::Active->value)
                    ->stateCast(new EnumStateCast(GetTestStatus::class)),
            ]);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$container, $sibling])
            ->fill();

        $fromOwnContainer = (new Get($container))($statePath);
        $fromSibling = (new Get($sibling))($statePath);

        expect($fromOwnContainer)
            ->toBe(GetTestStatus::Active)
            ->toEqual($fromSibling);
    });
});

describe('typed accessors', function (): void {
    it('can retrieve state as string via `string()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->string('name')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('John');
    });

    it('returns `null` from `string()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->string('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as integer via `integer()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('count')
                    ->default('42'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): int => $get->integer('count')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe(42);
    });

    it('returns `null` from `integer()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?int => $get->integer('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as float via `float()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('price')
                    ->default('3.14'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): float => $get->float('price')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe(3.14);
    });

    it('returns `null` from `float()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?float => $get->float('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as boolean via `boolean()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('active')
                    ->default('1'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): bool => $get->boolean('active')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeTrue();
    });

    it('returns `null` from `boolean()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?bool => $get->boolean('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as array via `array()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('items')
                    ->default(['a', 'b']),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => implode(',', $get->array('items'))),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('a,b');
    });

    it('returns `null` from `array()` when nullable and not array', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('scalar')
                    ->default('not-array'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->array('scalar', isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('can retrieve state as date via `date()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('date')
                    ->default('2024-01-15'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->date('date')->format('Y-m-d')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('2024-01-15');
    });

    it('returns `null` from `date()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->date('empty', isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('can retrieve state as enum via `enum()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('status')
                    ->default('active'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('status', GetTestStatus::class)?->value ?? 'null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('active');
    });

    it('returns `null` from `enum()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('empty', GetTestStatus::class, isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('returns existing `BackedEnum` instance from `enum()` without re-parsing', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('status')
                    ->default(GetTestStatus::Active),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('status', GetTestStatus::class)?->value ?? 'null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('active');
    });
});

describe('`filled()` and `blank()`', function (): void {
    it('returns `true` from `filled()` when state has a value', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->filled('name') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('yes');
    });

    it('returns `false` from `filled()` when state is blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->filled('empty') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('no');
    });

    it('returns `true` from `blank()` when state is blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->blank('empty') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('yes');
    });

    it('returns `false` from `blank()` when state has a value', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->blank('name') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('no');
    });
});

it('can set `skipComponentsChildContainersWhileSearching()`', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()));

    $get = new Get($component);

    $result = $get->skipComponentsChildContainersWhileSearching(false);

    expect($result)->toBe($get);
});

enum GetTestStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
