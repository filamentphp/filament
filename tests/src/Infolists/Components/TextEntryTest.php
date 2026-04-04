<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Test Name');
});

it('can format state using `formatStateUsing()`', function (): void {
    livewire(TestComponentWithFormattedTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('HELLO WORLD');
});

it('can display multiple values', function (): void {
    livewire(TestComponentWithMultipleTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Tag 1')
        ->assertSeeText('Tag 2')
        ->assertSeeText('Tag 3');
});

it('can set `badge()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isBadge())->toBeFalse();
    $entry->badge();
    expect($entry->isBadge())->toBeTrue();
});

it('can set `badge()` to false to undo', function (): void {
    $entry = TextEntry::make('name')->badge()->badge(false);
    expect($entry->isBadge())->toBeFalse();
});

it('can set `bulleted()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isBulleted())->toBeFalse();
    $entry->bulleted();
    expect($entry->isBulleted())->toBeTrue();
});

it('can set `listWithLineBreaks()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isListWithLineBreaks())->toBeFalse();
    $entry->listWithLineBreaks();
    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('`isListWithLineBreaks()` returns true when `bulleted()` is set', function (): void {
    $entry = TextEntry::make('name')->bulleted();
    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('can set `limitList()`', function (): void {
    $entry = TextEntry::make('name')->limitList(5);
    expect($entry->getListLimit())->toBe(5);
});

it('returns the default limit of `3` for `getListLimit()` when calling `limitList()` with no argument', function (): void {
    $entry = TextEntry::make('name')->limitList();
    expect($entry->getListLimit())->toBe(3);
});

it('returns `null` for `getListLimit()` by default', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->getListLimit())->toBeNull();
});

it('can set `prose()`', function (): void {
    livewire(TestComponentWithProseTextEntry::class)
        ->assertSuccessful();
});

it('can set `size()`', function (): void {
    $entry = TextEntry::make('name')->size(TextSize::Large);
    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('returns `TextSize::Small` for `getSize()` when no size is set', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->getSize(null))->toBe(TextSize::Small);
});

it('converts `"base"` string to `TextSize::Medium` in `getSize()`', function (): void {
    $entry = TextEntry::make('name')->size('base');
    expect($entry->getSize(null))->toBe(TextSize::Medium);
});

it('can set `expandableLimitedList()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isLimitedListExpandable())->toBeFalse();
    $entry->expandableLimitedList();
    expect($entry->isLimitedListExpandable())->toBeTrue();
});

it('returns `true` for `canWrapByDefault()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->canWrapByDefault())->toBeTrue();
});

it('can set `badge()` with a `Closure`', function (): void {
    $entry = TextEntry::make('title')
        ->badge(static fn (): bool => true);

    expect($entry->isBadge())->toBeTrue();
});

it('can set `limitList()` with a `Closure`', function (): void {
    $entry = TextEntry::make('tags')
        ->limitList(static fn (): int => 5);

    expect($entry->getListLimit())->toBe(5);
});

it('can set `size()` with a `Closure`', function (): void {
    $entry = TextEntry::make('title')
        ->size(static fn (): TextSize => TextSize::Large);

    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('can set `size()` with a string enum value', function (): void {
    $entry = TextEntry::make('title')
        ->size('lg');

    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('can set `expandableLimitedList()` with a `Closure`', function (): void {
    $entry = TextEntry::make('tags')
        ->expandableLimitedList(static fn (): bool => true);

    expect($entry->isLimitedListExpandable())->toBeTrue();
});

it('returns fluent `$this` from aggregate methods', function (): void {
    $entry = TextEntry::make('posts_count');

    expect($entry->avg('posts', 'rating'))->toBe($entry);
    expect($entry->counts('posts'))->toBe($entry);
    expect($entry->max('posts', 'rating'))->toBe($entry);
    expect($entry->min('posts', 'rating'))->toBe($entry);
    expect($entry->sum('posts', 'rating'))->toBe($entry);
});

it('defaults `isBulleted()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isBulleted())->toBeFalse();
});

it('defaults `isListWithLineBreaks()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isListWithLineBreaks())->toBeFalse();
});

it('defaults `isLimitedListExpandable()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isLimitedListExpandable())->toBeFalse();
});

describe('rendering', function (): void {
    it('can render with `badge()`', function (): void {
        livewire(RenderTextEntryWithBadge::class)->assertSuccessful();
    });

    it('can render with `badge()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureBadge::class)->assertSuccessful();
    });

    it('can render with `badge(false)` undone', function (): void {
        livewire(RenderTextEntryWithBadgeFalse::class)->assertSuccessful();
    });

    it('can render with `bulleted()`', function (): void {
        livewire(RenderTextEntryWithBulleted::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        livewire(RenderTextEntryWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `limitList()`', function (): void {
        livewire(RenderTextEntryWithLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` default', function (): void {
        livewire(RenderTextEntryWithDefaultLimitList::class)->assertSuccessful();
    });

    it('can render with `size()` enum', function (): void {
        livewire(RenderTextEntryWithSizeEnum::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `size()` string "base"', function (): void {
        livewire(RenderTextEntryWithSizeBase::class)->assertSuccessful();
    });

    it('can render with `size()` string enum value', function (): void {
        livewire(RenderTextEntryWithSizeString::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()`', function (): void {
        livewire(RenderTextEntryWithExpandableLimitedList::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureExpandableLimitedList::class)->assertSuccessful();
    });
});

it('has no accessibility issues in light mode', function (): void {
    retry(10, function (): void {
        Post::factory()->create();

        $this->actingAs(User::factory()->create());

        visit('/infolist-entries-browser-test')
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues in dark mode', function (): void {
    retry(10, function (): void {
        Post::factory()->create();

        $this->actingAs(User::factory()->create());

        visit('/infolist-entries-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class TestComponentWithTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'name' => 'Test Name',
            ])
            ->components([
                TextEntry::make('name'),
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

class TestComponentWithFormattedTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'message' => 'hello world',
            ])
            ->components([
                TextEntry::make('message')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
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

class TestComponentWithMultipleTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'tags' => ['Tag 1', 'Tag 2', 'Tag 3'],
            ])
            ->components([
                TextEntry::make('tags'),
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

class TestComponentWithProseTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'description' => '<p>Hello world</p>',
            ])
            ->components([
                TextEntry::make('description')->prose(),
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

class RenderTextEntryWithBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithBadgeFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge()->badge(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithBulleted extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B']])->components([TextEntry::make('tags')->bulleted()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithListWithLineBreaks extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B']])->components([TextEntry::make('tags')->listWithLineBreaks()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([TextEntry::make('tags')->limitList(2)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C']])->components([TextEntry::make('tags')->limitList(static fn (): int => 5)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithDefaultLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([TextEntry::make('tags')->limitList()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeEnum extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size(TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size(static fn (): TextSize => TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeBase extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size('base')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeString extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size('lg')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithExpandableLimitedList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([
            TextEntry::make('tags')->listWithLineBreaks()->limitList(2)->expandableLimitedList(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureExpandableLimitedList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([
            TextEntry::make('tags')->listWithLineBreaks()->limitList(2)->expandableLimitedList(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
