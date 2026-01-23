<?php

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can resolve `getConstantStatePath()` through nested `statePath()` with `constantState()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'parent' => [
                'child' => [
                    'data' => [
                        'key' => 'nested value',
                    ],
                ],
            ],
        ])
        ->components([
            Section::make()
                ->statePath('parent')
                ->schema([
                    Section::make()
                        ->statePath('child')
                        ->schema([
                            KeyValueEntry::make('data'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('parent.child.data');

    expect($entry->getStatePath())->toBe('parent.child.data')
        ->and($entry->getConstantStatePath())->toBe('parent.child.data')
        ->and($entry->getConstantState())->toBe(['key' => 'nested value']);
});

it('can resolve `getConstantStatePath()` when root schema has `statePath()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->constantState([
            'parent' => [
                'child' => [
                    'value' => 'nested value',
                ],
            ],
        ])
        ->components([
            Section::make()
                ->statePath('parent')
                ->schema([
                    Section::make()
                        ->statePath('child')
                        ->schema([
                            TextEntry::make('value'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('parent.child.value');

    expect($entry->getStatePath())->toBe('data.parent.child.value')
        ->and($entry->getConstantStatePath())->toBe('parent.child.value')
        ->and($entry->getConstantState())->toBe('nested value');
});

it('can resolve `getConstantStatePath()` through 3 levels of nested `statePath()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'level1' => [
                'level2' => [
                    'level3' => [
                        'data' => ['key' => 'deeply nested value'],
                    ],
                ],
            ],
        ])
        ->components([
            Section::make()
                ->statePath('level1')
                ->schema([
                    Section::make()
                        ->statePath('level2')
                        ->schema([
                            Section::make()
                                ->statePath('level3')
                                ->schema([
                                    KeyValueEntry::make('data'),
                                ]),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('level1.level2.level3.data');

    expect($entry->getStatePath())->toBe('level1.level2.level3.data')
        ->and($entry->getConstantStatePath())->toBe('level1.level2.level3.data')
        ->and($entry->getConstantState())->toBe(['key' => 'deeply nested value']);
});

it('can resolve `getConstantStatePath()` through 4 levels of nested `statePath()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'a' => ['b' => ['c' => ['d' => ['value' => 'four levels deep']]]],
        ])
        ->components([
            Section::make()->statePath('a')->schema([
                Section::make()->statePath('b')->schema([
                    Section::make()->statePath('c')->schema([
                        Section::make()->statePath('d')->schema([
                            TextEntry::make('value'),
                        ]),
                    ]),
                ]),
            ]),
        ]);

    $entry = $schema->getComponentByStatePath('a.b.c.d.value');

    expect($entry->getStatePath())->toBe('a.b.c.d.value')
        ->and($entry->getConstantStatePath())->toBe('a.b.c.d.value')
        ->and($entry->getConstantState())->toBe('four levels deep');
});

it('can resolve `getConstantStatePath()` through nested Grid components', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'parent' => ['child' => ['value' => 'grid nested']],
        ])
        ->components([
            Grid::make()
                ->statePath('parent')
                ->schema([
                    Grid::make()
                        ->statePath('child')
                        ->schema([
                            TextEntry::make('value'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('parent.child.value');

    expect($entry->getConstantStatePath())->toBe('parent.child.value')
        ->and($entry->getConstantState())->toBe('grid nested');
});

it('can resolve `getConstantStatePath()` through nested Group components', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'outer' => ['inner' => ['value' => 'group nested']],
        ])
        ->components([
            Group::make()
                ->statePath('outer')
                ->schema([
                    Group::make()
                        ->statePath('inner')
                        ->schema([
                            TextEntry::make('value'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('outer.inner.value');

    expect($entry->getConstantStatePath())->toBe('outer.inner.value')
        ->and($entry->getConstantState())->toBe('group nested');
});

it('can resolve `getConstantStatePath()` through mixed nested layout components', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'section' => ['grid' => ['group' => ['value' => 'mixed layouts']]],
        ])
        ->components([
            Section::make()
                ->statePath('section')
                ->schema([
                    Grid::make()
                        ->statePath('grid')
                        ->schema([
                            Group::make()
                                ->statePath('group')
                                ->schema([
                                    TextEntry::make('value'),
                                ]),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('section.grid.group.value');

    expect($entry->getConstantStatePath())->toBe('section.grid.group.value')
        ->and($entry->getConstantState())->toBe('mixed layouts');
});

it('can resolve `getConstantStatePath()` when schema has `record()`', function (): void {
    $record = new Post(['title' => 'Test Post', 'content' => 'Post content']);

    $schema = Schema::make(Livewire::make())
        ->record($record)
        ->components([
            Section::make()
                ->statePath('nested')
                ->schema([
                    TextEntry::make('field'),
                ]),
        ]);

    // When schema has `record()`, `getConstantStatePath()` returns `getStatePath()`
    expect($schema->getConstantStatePath())->toBe($schema->getStatePath());
});

it('can resolve `getConstantStatePath()` through deeply nested sections with `record()`', function (): void {
    $record = new Post(['title' => 'Test Post', 'content' => 'Post content']);

    $schema = Schema::make(Livewire::make())
        ->record($record)
        ->components([
            Section::make()
                ->statePath('nested')
                ->schema([
                    Section::make()
                        ->statePath('deep')
                        ->schema([
                            TextEntry::make('field'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('nested.deep.field');

    // Entry should have the correct state path
    expect($entry->getStatePath())->toBe('nested.deep.field');
});

it('can resolve `getConstantStatePath()` through layouts without `statePath()` that inherit parent path', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'parent' => [
                'value' => 'inherited path value',
            ],
        ])
        ->components([
            Section::make()
                ->statePath('parent')
                ->schema([
                    // This Section has no statePath, so it inherits from parent
                    Section::make()
                        ->schema([
                            TextEntry::make('value'),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('parent.value');

    expect($entry->getStatePath())->toBe('parent.value')
        ->and($entry->getConstantStatePath())->toBe('parent.value')
        ->and($entry->getConstantState())->toBe('inherited path value');
});

it('can resolve `getConstantStatePath()` with mixed layouts some with and some without `statePath()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'level1' => [
                'level2' => [
                    'value' => 'mixed inheritance value',
                ],
            ],
        ])
        ->components([
            Section::make()
                ->statePath('level1')
                ->schema([
                    // Grid without statePath - inherits 'level1'
                    Grid::make()
                        ->schema([
                            Section::make()
                                ->statePath('level2')
                                ->schema([
                                    // Group without statePath - inherits 'level1.level2'
                                    Group::make()
                                        ->schema([
                                            TextEntry::make('value'),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('level1.level2.value');

    expect($entry->getStatePath())->toBe('level1.level2.value')
        ->and($entry->getConstantStatePath())->toBe('level1.level2.value')
        ->and($entry->getConstantState())->toBe('mixed inheritance value');
});

it('can resolve `getConstantStatePath()` through multiple layouts without `statePath()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->constantState([
            'root' => [
                'value' => 'deeply inherited value',
            ],
        ])
        ->components([
            Section::make()
                ->statePath('root')
                ->schema([
                    // None of these have statePath, all inherit 'root'
                    Grid::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('value'),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);

    $entry = $schema->getComponentByStatePath('root.value');

    expect($entry->getStatePath())->toBe('root.value')
        ->and($entry->getConstantStatePath())->toBe('root.value')
        ->and($entry->getConstantState())->toBe('deeply inherited value');
});
