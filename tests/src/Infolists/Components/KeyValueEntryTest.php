<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithKeyValueEntry::class)
        ->assertSuccessful()
        ->assertSeeText('name')
        ->assertSeeText('John Doe')
        ->assertSeeText('email')
        ->assertSeeText('john@example.com');
});

it('can render with custom key and value labels', function (): void {
    livewire(TestComponentWithLabeledKeyValueEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Setting')
        ->assertSeeText('Value');
});

it('can set and get `keyLabel()`', function (): void {
    $entry = KeyValueEntry::make('metadata')->keyLabel('Property');
    expect($entry->getKeyLabel())->toBe('Property');
});

it('can set and get `valueLabel()`', function (): void {
    $entry = KeyValueEntry::make('metadata')->valueLabel('Content');
    expect($entry->getValueLabel())->toBe('Content');
});

it('`getKeyLabel()` returns the translated default when not set', function (): void {
    $entry = KeyValueEntry::make('metadata');
    expect($entry->getKeyLabel())->toBe(__('filament-infolists::components.entries.key_value.columns.key.label'));
});

it('`getValueLabel()` returns the translated default when not set', function (): void {
    $entry = KeyValueEntry::make('metadata');
    expect($entry->getValueLabel())->toBe(__('filament-infolists::components.entries.key_value.columns.value.label'));
});

it('can set `keyLabel()` using a `Closure`', function (): void {
    $entry = KeyValueEntry::make('metadata')->keyLabel(static fn (): string => 'Dynamic Key');
    expect($entry->getKeyLabel())->toBe('Dynamic Key');
});

it('can set `valueLabel()` using a `Closure`', function (): void {
    $entry = KeyValueEntry::make('metadata')->valueLabel(static fn (): string => 'Dynamic Value');
    expect($entry->getValueLabel())->toBe('Dynamic Value');
});

it('returns fluent `$this` from `keyLabel()`', function (): void {
    $entry = KeyValueEntry::make('metadata');
    expect($entry->keyLabel('Key'))->toBe($entry);
});

it('can use deprecated `emptyMessage()` as an alias for `placeholder()`', function (): void {
    $entry = KeyValueEntry::make('metadata')->emptyMessage('No data available');
    expect($entry->getPlaceholder())->toBe('No data available');
});

describe('rendering', function (): void {
    it('can render with `keyLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueEntryWithClosureKeyLabel::class)
            ->assertSuccessful()
            ->assertSeeText('Dynamic Key');
    });

    it('can render with `valueLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueEntryWithClosureValueLabel::class)
            ->assertSuccessful()
            ->assertSeeText('Dynamic Value');
    });

    it('can render with `emptyMessage()` as placeholder', function (): void {
        livewire(RenderKeyValueEntryWithEmptyMessage::class)
            ->assertSuccessful()
            ->assertSeeText('No data available');
    });
});

class TestComponentWithKeyValueEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'metadata' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ])
            ->components([
                KeyValueEntry::make('metadata'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithLabeledKeyValueEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'settings' => [
                    'theme' => 'dark',
                    'language' => 'en',
                ],
            ])
            ->components([
                KeyValueEntry::make('settings')
                    ->keyLabel('Setting')
                    ->valueLabel('Value'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class RenderKeyValueEntryWithClosureKeyLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['metadata' => ['name' => 'John']])->components([
            KeyValueEntry::make('metadata')->keyLabel(static fn (): string => 'Dynamic Key'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderKeyValueEntryWithClosureValueLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['metadata' => ['name' => 'John']])->components([
            KeyValueEntry::make('metadata')->valueLabel(static fn (): string => 'Dynamic Value'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderKeyValueEntryWithEmptyMessage extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['metadata' => []])->components([
            KeyValueEntry::make('metadata')->emptyMessage('No data available'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
