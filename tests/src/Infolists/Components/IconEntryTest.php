<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithIconEntry::class)
        ->assertOk();
});

it('can render icon entry with state-based `url()`', function (): void {
    livewire(IconEntryWithUrl::class)
        ->assertOk()
        ->assertSeeHtml('href="https://example.com/icon-link"');
});

it('can set `icon()`', function (): void {
    $entry = IconEntry::make('status')
        ->icon(Heroicon::Check);

    expect($entry->getIcon(null))->toBe(Heroicon::Check);
});

it('can set `size()`', function (): void {
    $entry = IconEntry::make('status')
        ->size(IconSize::Large);

    expect($entry->getSize(null))->toBe(IconSize::Large);
});

it('can set `boolean()` mode with `trueIcon()` and `falseIcon()`', function (): void {
    $entry = IconEntry::make('is_active')
        ->boolean()
        ->trueIcon(Heroicon::Check)
        ->falseIcon(Heroicon::XMark);

    expect($entry->getTrueIcon())->toBe(Heroicon::Check);
    expect($entry->getFalseIcon())->toBe(Heroicon::XMark);
});

it('can set `trueColor()` and `falseColor()`', function (): void {
    $entry = IconEntry::make('is_active')
        ->boolean()
        ->trueColor('success')
        ->falseColor('danger');

    expect($entry->getTrueColor())->toBe('success');
    expect($entry->getFalseColor())->toBe('danger');
});

it('can set `boolean()`', function (): void {
    $entry = IconEntry::make('is_active')->boolean();
    expect($entry->isBoolean())->toBeTrue();
});

it('can set `boolean()` to false to undo', function (): void {
    $entry = IconEntry::make('is_active')->boolean()->boolean(false);
    expect($entry->isBoolean())->toBeFalse();
});

it('can set icon and color via `true()` helper', function (): void {
    $entry = IconEntry::make('is_active')
        ->true(Heroicon::Check, 'success');

    expect($entry->getTrueIcon())->toBe(Heroicon::Check);
    expect($entry->getTrueColor())->toBe('success');
});

it('can set icon and color via `false()` helper', function (): void {
    $entry = IconEntry::make('is_active')
        ->false(Heroicon::XMark, 'danger');

    expect($entry->getFalseIcon())->toBe(Heroicon::XMark);
    expect($entry->getFalseColor())->toBe('danger');
});

it('returns default `"success"` for `getTrueColor()` when not set', function (): void {
    $entry = IconEntry::make('is_active')->boolean();
    expect($entry->getTrueColor())->toBe('success');
});

it('returns default `"danger"` for `getFalseColor()` when not set', function (): void {
    $entry = IconEntry::make('is_active')->boolean();
    expect($entry->getFalseColor())->toBe('danger');
});

it('`getIcon()` returns the base icon when set directly', function (): void {
    $entry = IconEntry::make('is_active')->icon(Heroicon::Check);
    expect($entry->getIcon('anything'))->toBe(Heroicon::Check);
});

it('`getIcon()` returns the `trueIcon` for a truthy state when `boolean()` is set', function (): void {
    $entry = IconEntry::make('is_active')
        ->boolean()
        ->trueIcon(Heroicon::Check)
        ->falseIcon(Heroicon::XMark);

    expect($entry->getIcon(true))->toBe(Heroicon::Check);
    expect($entry->getIcon(false))->toBe(Heroicon::XMark);
});

it('`getColor()` returns the `trueColor` for a truthy state when `boolean()` is set', function (): void {
    $entry = IconEntry::make('is_active')
        ->boolean()
        ->trueColor('success')
        ->falseColor('danger');

    expect($entry->getColor(true))->toBe('success');
    expect($entry->getColor(false))->toBe('danger');
});

it('can set `listWithLineBreaks()`', function (): void {
    $entry = IconEntry::make('status');
    expect($entry->isListWithLineBreaks())->toBeFalse();
    $entry->listWithLineBreaks();
    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('returns `true` for `canWrapByDefault()`', function (): void {
    $entry = IconEntry::make('status');
    expect($entry->canWrapByDefault())->toBeTrue();
});

it('can set `boolean()` with a `Closure`', function (): void {
    $entry = IconEntry::make('active')
        ->boolean(static fn (): bool => true);

    expect($entry->isBoolean())->toBeTrue();
});

it('can set `size()` with a `Closure`', function (): void {
    $entry = IconEntry::make('status')
        ->size(static fn (): IconSize => IconSize::Large);

    expect($entry->getSize(null))->toBe(IconSize::Large);
});

it('defaults `getSize()` to `null`', function (): void {
    expect(IconEntry::make('status')->getSize(null))->toBeNull();
});

it('can set `size()` with a string value', function (): void {
    $entry = IconEntry::make('status')
        ->size('lg');

    expect($entry->getSize(null))->toBe('lg');
});

it('can set `listWithLineBreaks()` with a `Closure`', function (): void {
    $entry = IconEntry::make('icons')
        ->listWithLineBreaks(static fn (): bool => true);

    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('defaults `isListWithLineBreaks()` to `false`', function (): void {
    expect(IconEntry::make('icons')->isListWithLineBreaks())->toBeFalse();
});

it('returns fluent `$this` from `trueColor()`', function (): void {
    $entry = IconEntry::make('active');

    expect($entry->trueColor('success'))->toBe($entry);
});

it('returns fluent `$this` from `falseIcon()`', function (): void {
    $entry = IconEntry::make('active');

    expect($entry->falseIcon('heroicon-o-x-mark'))->toBe($entry);
});

it('can set `trueColor()` with a `Closure`', function (): void {
    $entry = IconEntry::make('active')
        ->boolean()
        ->trueColor(static fn (): string => 'info');

    expect($entry->getTrueColor())->toBe('info');
});

it('can set `falseColor()` with a `Closure`', function (): void {
    $entry = IconEntry::make('active')
        ->boolean()
        ->falseColor(static fn (): string => 'warning');

    expect($entry->getFalseColor())->toBe('warning');
});

it('can set `trueIcon()` with a `Closure`', function (): void {
    $entry = IconEntry::make('active')
        ->boolean()
        ->trueIcon(static fn () => Heroicon::Check);

    expect($entry->getTrueIcon())->toBe(Heroicon::Check);
});

it('can set `falseIcon()` with a `Closure`', function (): void {
    $entry = IconEntry::make('active')
        ->boolean()
        ->falseIcon(static fn () => Heroicon::XMark);

    expect($entry->getFalseIcon())->toBe(Heroicon::XMark);
});

describe('rendering', function (): void {
    it('can render with `size()`', function (): void {
        livewire(RenderIconEntryWithSize::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `boolean()` mode', function (): void {
        livewire(RenderIconEntryWithBoolean::class)->assertSuccessful();
    });

    it('can render with `boolean()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureBoolean::class)->assertSuccessful();
    });

    it('can render with `boolean(false)` undo', function (): void {
        livewire(RenderIconEntryWithBooleanUndone::class)->assertSuccessful();
    });

    it('can render with `trueColor()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureTrueColor::class)->assertSuccessful();
    });

    it('can render with `falseColor()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureFalseColor::class)->assertSuccessful();
    });

    it('can render with `trueIcon()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureTrueIcon::class)->assertSuccessful();
    });

    it('can render with `falseIcon()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureFalseIcon::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        livewire(RenderIconEntryWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()` set via `Closure`', function (): void {
        livewire(RenderIconEntryWithClosureListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `true()` combined icon and color', function (): void {
        livewire(RenderIconEntryWithTrueCombined::class)->assertSuccessful();
    });

    it('can render with `false()` combined icon and color', function (): void {
        livewire(RenderIconEntryWithFalseCombined::class)->assertSuccessful();
    });
});

class TestComponentWithIconEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'status' => 'active',
            ])
            ->components([
                IconEntry::make('status')
                    ->icon(Heroicon::Check),
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

class IconEntryWithUrl extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'icon_with_url' => 'https://example.com/icon-link',
            ])
            ->components([
                IconEntry::make('icon_with_url')
                    ->icon(Heroicon::Link)
                    ->url(static fn (?string $state): ?string => $state),
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

class RenderIconEntryWithSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['status' => 'active'])->components([
            IconEntry::make('status')->icon(Heroicon::Check)->size(IconSize::Large),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['status' => 'active'])->components([
            IconEntry::make('status')->icon(Heroicon::Check)->size(static fn (): IconSize => IconSize::Large),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithBoolean extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => true])->components([
            IconEntry::make('is_active')->boolean(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureBoolean extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => true])->components([
            IconEntry::make('is_active')->boolean(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithBooleanUndone extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['status' => 'active'])->components([
            IconEntry::make('status')->icon(Heroicon::Check)->boolean()->boolean(false),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureTrueColor extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => true])->components([
            IconEntry::make('is_active')->boolean()->trueColor(static fn (): string => 'info'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureFalseColor extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => false])->components([
            IconEntry::make('is_active')->boolean()->falseColor(static fn (): string => 'warning'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureTrueIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => true])->components([
            IconEntry::make('is_active')->boolean()->trueIcon(static fn () => Heroicon::Check),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureFalseIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => false])->components([
            IconEntry::make('is_active')->boolean()->falseIcon(static fn () => Heroicon::XMark),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithListWithLineBreaks extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['status' => 'active'])->components([
            IconEntry::make('status')->icon(Heroicon::Check)->listWithLineBreaks(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithClosureListWithLineBreaks extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['status' => 'active'])->components([
            IconEntry::make('status')->icon(Heroicon::Check)->listWithLineBreaks(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithTrueCombined extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => true])->components([
            IconEntry::make('is_active')->boolean()->true(Heroicon::Check, 'success'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderIconEntryWithFalseCombined extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['is_active' => false])->components([
            IconEntry::make('is_active')->boolean()->false(Heroicon::XMark, 'danger'),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
