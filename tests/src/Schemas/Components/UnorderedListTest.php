<?php

use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with an empty schema', function (): void {
    $list = UnorderedList::make();

    expect($list)->toBeInstanceOf(UnorderedList::class);
});

it('can be constructed with an array schema', function (): void {
    $list = UnorderedList::make([
        'Item 1',
        'Item 2',
    ]);

    expect($list)->toBeInstanceOf(UnorderedList::class);
});

describe('size', function (): void {
    it('returns `null` for `getSize()` by default', function (): void {
        $list = UnorderedList::make();

        expect($list->getSize())->toBeNull();
    });

    it('can set `size()` with a `TextSize` enum', function (): void {
        $list = UnorderedList::make()->size(TextSize::Large);

        expect($list->getSize())->toBe(TextSize::Large);
    });

    it('can set `size()` with a string that maps to a `TextSize` enum', function (): void {
        $list = UnorderedList::make()->size('lg');

        expect($list->getSize())->toBe(TextSize::Large);
    });

    it('can set `size()` with a custom string that does not map to an enum', function (): void {
        $list = UnorderedList::make()->size('custom');

        expect($list->getSize())->toBe('custom');
    });

    it('can set `size()` with a `Closure`', function (): void {
        $list = UnorderedList::make()
            ->size(static fn (): TextSize => TextSize::Small);

        expect($list->getSize())->toBe(TextSize::Small);
    });

    it('can clear `size()` with `null`', function (): void {
        $list = UnorderedList::make()
            ->size(TextSize::Large)
            ->size(null);

        expect($list->getSize())->toBeNull();
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderUnorderedList::class)->assertSuccessful();
    });

    it('can render with `size()` enum', function (): void {
        livewire(RenderUnorderedListWithSize::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        livewire(RenderUnorderedListWithClosureSize::class)->assertSuccessful();
    });
});

class RenderUnorderedList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([UnorderedList::make(['Item 1', 'Item 2'])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderUnorderedListWithSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([UnorderedList::make(['Item'])->size(TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderUnorderedListWithClosureSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([UnorderedList::make(['Item'])->size(static fn (): TextSize => TextSize::Small)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
