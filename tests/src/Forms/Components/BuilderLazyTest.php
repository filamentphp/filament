<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('marks Builder as collapsible when `lazy()` is enabled', function (): void {
    $builder = Builder::make('content')->lazy();

    expect($builder->isLazy())->toBeTrue()
        ->and($builder->isCollapsible())->toBeTrue()
        ->and($builder->isCollapsed())->toBeTrue()
        ->and($builder->shouldUnloadOnCollapse())->toBeFalse();
});

it('exposes the `unloadOnCollapse` argument via `shouldUnloadOnCollapse()`', function (): void {
    $builder = Builder::make('content')->lazy(unloadOnCollapse: true);

    expect($builder->shouldUnloadOnCollapse())->toBeTrue();
});

it('renders a placeholder instead of fields for lazy items on first render', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithLazyBuilder::class)
        ->assertSeeHtml('fi-fo-builder-item-placeholder')
        ->assertSeeHtml('fi-loading-indicator')
        ->assertDontSeeHtml('wire:model="data.blocks.0.data.foo"')
        ->assertDontSeeHtml('wire:model="data.blocks.1.data.foo"');

    $undoBuilderFake();
});

it('renders fields after `loadItem` is called', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithLazyBuilder::class)
        ->call('callSchemaComponentMethod', 'form.lazyBuilder', 'loadItem', ['itemKey' => '0'])
        ->assertSeeHtml('wire:model="data.blocks.0.data.foo"')
        ->assertDontSeeHtml('wire:model="data.blocks.1.data.foo"');

    $undoBuilderFake();
});

it('removes fields again after `unloadItem` is called', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithLazyBuilder::class)
        ->call('callSchemaComponentMethod', 'form.lazyBuilder', 'loadItem', ['itemKey' => '0'])
        ->assertSeeHtml('wire:model="data.blocks.0.data.foo"')
        ->call('callSchemaComponentMethod', 'form.lazyBuilder', 'unloadItem', ['itemKey' => '0'])
        ->assertDontSeeHtml('wire:model="data.blocks.0.data.foo"');

    $undoBuilderFake();
});

it('lets `Block::lazy(false)` opt a block out of a lazy Builder', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithLazyBuilderAndEagerBlock::class)
        // The eager block renders its field straight away.
        ->assertSeeHtml('wire:model="data.blocks.0.data.foo"')
        // The lazy block does not.
        ->assertDontSeeHtml('wire:model="data.blocks.1.data.foo"');

    $undoBuilderFake();
});

it('auto-loads items containing validation errors', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithLazyBuilder::class)
        ->call('save')
        ->assertHasErrors([
            'data.blocks.0.data.foo',
            'data.blocks.1.data.foo',
        ])
        // Both items have rendered their fields after validation failed,
        // even though they started in the collapsed/unloaded state.
        ->assertSeeHtml('wire:model="data.blocks.0.data.foo"')
        ->assertSeeHtml('wire:model="data.blocks.1.data.foo"');

    $undoBuilderFake();
});

class TestComponentWithLazyBuilder extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->key('lazyBuilder')
                    ->lazy(unloadOnCollapse: true)
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo')->required(),
                            ]),
                    ])
                    ->default([
                        ['type' => 'one', 'data' => ['foo' => '']],
                        ['type' => 'one', 'data' => ['foo' => '']],
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithLazyBuilderAndEagerBlock extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->key('mixedBuilder')
                    ->lazy()
                    ->blocks([
                        Builder\Block::make('eager')
                            ->lazy(false)
                            ->schema([
                                TextInput::make('foo'),
                            ]),
                        Builder\Block::make('lazy')
                            ->schema([
                                TextInput::make('foo'),
                            ]),
                    ])
                    ->default([
                        ['type' => 'eager', 'data' => ['foo' => '']],
                        ['type' => 'lazy', 'data' => ['foo' => '']],
                    ]),
            ])
            ->statePath('data');
    }
}
