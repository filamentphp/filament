<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithColorEntry::class)
        ->assertSuccessful();
});

it('can render with hex color', function (): void {
    livewire(TestComponentWithHexColorEntry::class)
        ->assertSuccessful();
});

it('can set `copyable()`', function (): void {
    $entry = ColorEntry::make('color');
    expect($entry->isCopyable(null))->toBeFalse();
    $entry->copyable();
    expect($entry->isCopyable(null))->toBeTrue();
});

it('can set `copyMessage()`', function (): void {
    $entry = ColorEntry::make('color')->copyMessage('Color copied!');
    expect($entry->getCopyMessage(null))->toBe('Color copied!');
});

it('can set `copyMessageDuration()`', function (): void {
    $entry = ColorEntry::make('color')->copyMessageDuration(5000);
    expect($entry->getCopyMessageDuration(null))->toBe(5000);
});

it('has a default `getCopyMessageDuration()` of `2000`', function (): void {
    $entry = ColorEntry::make('color');
    expect($entry->getCopyMessageDuration(null))->toBe(2000);
});

it('returns `true` for `canWrapByDefault()`', function (): void {
    $entry = ColorEntry::make('color');
    expect($entry->canWrapByDefault())->toBeTrue();
});

describe('wrapping', function (): void {
    it('defaults `canWrap()` to `true` via `canWrapByDefault()`', function (): void {
        $entry = ColorEntry::make('color');

        expect($entry->canWrap())->toBeTrue();
    });

    it('can set `wrap()` to `false`', function (): void {
        $entry = ColorEntry::make('color')->wrap(false);

        expect($entry->canWrap())->toBeFalse();
    });

    it('can set `wrap()` with a `Closure`', function (): void {
        $entry = ColorEntry::make('color')
            ->wrap(static fn (): bool => false);

        expect($entry->canWrap())->toBeFalse();
    });

    it('falls back to `canWrapByDefault()` when `wrap()` is `null`', function (): void {
        $entry = ColorEntry::make('color')
            ->wrap(false)
            ->wrap(null);

        expect($entry->canWrap())->toBeTrue();
    });
});

describe('copy Closure support', function (): void {
    it('can set `copyable()` with a `Closure`', function (): void {
        $entry = ColorEntry::make('color')
            ->copyable(static fn (): bool => true);

        expect($entry->isCopyable(null))->toBeTrue();
    });

    it('can set `copyMessage()` with a `Closure`', function (): void {
        $entry = ColorEntry::make('color')
            ->copyMessage(static fn (): string => 'Dynamic message');

        expect($entry->getCopyMessage(null))->toBe('Dynamic message');
    });

    it('can set `copyMessageDuration()` with a `Closure`', function (): void {
        $entry = ColorEntry::make('color')
            ->copyMessageDuration(static fn (): int => 3000);

        expect($entry->getCopyMessageDuration(null))->toBe(3000);
    });
});

describe('copyable state', function (): void {
    it('returns `null` from `getCopyableState()` by default', function (): void {
        $entry = ColorEntry::make('color');

        expect($entry->getCopyableState(null))->toBeNull();
    });

    it('can set `copyableState()`', function (): void {
        $entry = ColorEntry::make('color')
            ->copyableState('custom-value');

        expect($entry->getCopyableState(null))->toBe('custom-value');
    });

    it('can set `copyableState()` with a `Closure` that receives `$state`', function (): void {
        $entry = ColorEntry::make('color')
            ->copyableState(static fn (mixed $state): string => "Color: {$state}");

        expect($entry->getCopyableState('#ff0000'))->toBe('Color: #ff0000');
    });
});

describe('rendering', function (): void {
    it('can render with `copyable()`', function (): void {
        livewire(RenderColorEntryWithCopyable::class)->assertSuccessful();
    });

    it('can render with `copyable()` set via `Closure`', function (): void {
        livewire(RenderColorEntryWithClosureCopyable::class)->assertSuccessful();
    });

    it('can render with `copyMessage()`', function (): void {
        livewire(RenderColorEntryWithCopyMessage::class)->assertSuccessful();
    });

    it('can render with `copyMessage()` set via `Closure`', function (): void {
        livewire(RenderColorEntryWithClosureCopyMessage::class)->assertSuccessful();
    });

    it('can render with `copyMessageDuration()`', function (): void {
        livewire(RenderColorEntryWithCopyMessageDuration::class)->assertSuccessful();
    });

    it('can render with `copyMessageDuration()` set via `Closure`', function (): void {
        livewire(RenderColorEntryWithClosureCopyMessageDuration::class)->assertSuccessful();
    });

    it('can render with `copyableState()`', function (): void {
        livewire(RenderColorEntryWithCopyableState::class)->assertSuccessful();
    });

    it('can render with `wrap(false)`', function (): void {
        livewire(RenderColorEntryWithWrapFalse::class)->assertSuccessful();
    });

    it('can render with `wrap()` set via `Closure`', function (): void {
        livewire(RenderColorEntryWithClosureWrap::class)->assertSuccessful();
    });

    it('can render with `wrap(null)` fallback', function (): void {
        livewire(RenderColorEntryWithWrapNull::class)->assertSuccessful();
    });
});

class TestComponentWithColorEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'color' => '#ff0000',
            ])
            ->components([
                ColorEntry::make('color'),
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

class TestComponentWithHexColorEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'primary_color' => '#3b82f6',
            ])
            ->components([
                ColorEntry::make('primary_color'),
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

class RenderColorEntryWithCopyable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithClosureCopyable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithCopyMessage extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable()->copyMessage('Color copied!'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithClosureCopyMessage extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable()->copyMessage(static fn (): string => 'Dynamic message'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithCopyMessageDuration extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable()->copyMessageDuration(5000),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithClosureCopyMessageDuration extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable()->copyMessageDuration(static fn (): int => 3000),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithCopyableState extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->copyable()->copyableState('custom-value'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithWrapFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->wrap(false),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithClosureWrap extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->wrap(static fn (): bool => false),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderColorEntryWithWrapNull extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['color' => '#ff0000'])->components([
            ColorEntry::make('color')->wrap(false)->wrap(null),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
