<?php

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
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
