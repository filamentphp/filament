<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnStaticUrl;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnUrl;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnUrlNewTab;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('boolean configuration', function (): void {
    it('can automatically detect boolean cast attribute in icon column', function (): void {
        $post = Post::factory()
            ->create(['is_published' => false]);

        livewire(TestTableWithImplicitBooleanIconColumn::class)
            ->assertTableColumnExists('is_published', function (IconColumn $column) {
                return $column->isBoolean();
            }, $post);
    });

    it('can set `boolean()` and get with `isBoolean()`', function (): void {
        expect(IconColumn::make('active')->boolean()->isBoolean())->toBeTrue();
    });

    it('can undo `boolean()` with `false`', function (): void {
        expect(IconColumn::make('active')->boolean()->boolean(false)->isBoolean())->toBeFalse();
    });

    it('can set `boolean()` with a `Closure`', function (): void {
        expect(IconColumn::make('active')->boolean(static fn (): bool => true)->isBoolean())->toBeTrue();
    });
});

describe('size', function (): void {
    it('can set `size()` and get with `getSize()`', function (): void {
        expect(IconColumn::make('active')->size(IconSize::Small)->getSize(null))->toBe(IconSize::Small);
    });

    it('defaults `getSize()` to `null` when not set', function (): void {
        expect(IconColumn::make('active')->getSize(null))->toBeNull();
    });

    it('can set `size()` with a `Closure`', function (): void {
        expect(IconColumn::make('active')->size(static fn (): IconSize => IconSize::Large)->getSize(null))->toBe(IconSize::Large);
    });

    it('can set `size()` with a string enum value', function (): void {
        expect(IconColumn::make('active')->size('lg')->getSize(null))->toBe(IconSize::Large);
    });
});

describe('boolean colors and icons', function (): void {
    it('can set `falseColor()` and get with `getFalseColor()`', function (): void {
        expect(IconColumn::make('active')->falseColor('warning')->getFalseColor())->toBe('warning');
    });

    it('defaults `getFalseColor()` to `danger`', function (): void {
        expect(IconColumn::make('active')->boolean()->getFalseColor())->toBe('danger');
    });

    it('can set `falseIcon()` and get with `getFalseIcon()`', function (): void {
        expect(IconColumn::make('active')->falseIcon('heroicon-o-x-mark')->getFalseIcon())->toBe('heroicon-o-x-mark');
    });

    it('can set `trueColor()` and get with `getTrueColor()`', function (): void {
        expect(IconColumn::make('active')->trueColor('info')->getTrueColor())->toBe('info');
    });

    it('defaults `getTrueColor()` to `success`', function (): void {
        expect(IconColumn::make('active')->boolean()->getTrueColor())->toBe('success');
    });

    it('can set `trueIcon()` and get with `getTrueIcon()`', function (): void {
        expect(IconColumn::make('active')->trueIcon('heroicon-o-check')->getTrueIcon())->toBe('heroicon-o-check');
    });
});

describe('list layout', function (): void {
    it('can set `listWithLineBreaks()` and get with `isListWithLineBreaks()`', function (): void {
        expect(IconColumn::make('active')->listWithLineBreaks()->isListWithLineBreaks())->toBeTrue();
    });

    it('defaults `isListWithLineBreaks()` to `false`', function (): void {
        expect(IconColumn::make('active')->isListWithLineBreaks())->toBeFalse();
    });
});

describe('rendering boolean states', function (): void {
    it('can render', function (): void {
        Post::factory()->count(5)->create();

        livewire(TestTableWithIconColumn::class)
            ->assertSuccessful()
            ->assertCanRenderTableColumn('is_published');
    });

    it('can display true state', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(TestTableWithIconColumn::class)
            ->assertSuccessful();
    });

    it('can display false state', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithIconColumn::class)
            ->assertSuccessful();
    });
});

describe('state and related record injection', function (): void {
    it('injects `$state` and `$relatedRecord` into boolean configuration', function (): void {
        $relatedRecord = new User(['email' => 'user@example.com']);

        $column = IconColumn::make('active')
            ->boolean(static fn (bool $state, User $relatedRecord): bool => is_bool($state) && ($relatedRecord->email === 'user@example.com'))
            ->trueColor(static fn (bool $state, User $relatedRecord): string => ($state && ($relatedRecord->email === 'user@example.com')) ? 'info' : 'danger')
            ->falseColor(static fn (bool $state, User $relatedRecord): string => ((! $state) && ($relatedRecord->email === 'user@example.com')) ? 'warning' : 'danger')
            ->trueIcon(static fn (bool $state, User $relatedRecord): string => ($state && ($relatedRecord->email === 'user@example.com')) ? 'true-icon' : 'wrong-icon')
            ->falseIcon(static fn (bool $state, User $relatedRecord): string => ((! $state) && ($relatedRecord->email === 'user@example.com')) ? 'false-icon' : 'wrong-icon');

        expect($column->getColor(true, $relatedRecord))->toBe('info')
            ->and($column->getColor(false, $relatedRecord))->toBe('warning')
            ->and($column->getIcon(true, $relatedRecord))->toBe('true-icon')
            ->and($column->getIcon(false, $relatedRecord))->toBe('false-icon');
    });

    it('preserves implicit `$state` injection when boolean getter state is omitted', function (): void {
        $column = IconColumn::make('active')
            ->table(livewire(TestTableWithIconColumn::class)->instance()->getTable())
            ->record(new User)
            ->state(true)
            ->trueColor(static fn (?bool $state): string => $state ? 'info' : 'danger');

        expect($column->getTrueColor())->toBe('info')
            ->and($column->getTrueColor(null))->toBe('danger');
    });
});

describe('icon maps', function (): void {
    it('resolves `Arrayable` icon maps directly and from lazy closures', function (string $type, bool $isLazy): void {
        $record = new User(['name' => 'Example']);
        $icons = [
            'heroicon-o-question-mark-circle',
            'heroicon-o-pencil' => 'draft',
            'heroicon-o-clock' => static fn (mixed $state): bool => in_array($state, ['draft', 'reviewing'], true),
            'heroicon-o-check-circle' => 'published',
            'heroicon-o-user' => $record,
        ];
        $icons = match ($type) {
            'collection' => collect($icons),
            'arrayable' => new class($icons) implements Arrayable
            {
                public function __construct(private array $icons) {}

                public function toArray(): array
                {
                    return $this->icons;
                }
            },
            default => $icons,
        };
        $evaluatedStates = [];
        $configuration = $isLazy ? static function (mixed $state) use ($icons, &$evaluatedStates) {
            $evaluatedStates[] = $state;

            return $icons;
        } : $icons;
        $column = IconColumn::make('status')->boolean(false);

        expect($column->icons($configuration))->toBe($column)
            ->and($evaluatedStates)->toBe([])
            ->and($column->getIcon('draft'))->toBe('heroicon-o-clock')
            ->and($column->getIcon('reviewing'))->toBe('heroicon-o-clock')
            ->and($column->getIcon('published'))->toBe('heroicon-o-check-circle')
            ->and($column->getIcon('other'))->toBe('heroicon-o-question-mark-circle')
            ->and($column->getIcon($record))->toBe('heroicon-o-user')
            ->and($evaluatedStates)->toBe($isLazy ? ['draft', 'reviewing', 'published', 'other', $record] : []);
    })->with(['array', 'collection', 'arrayable'])->with([false, true]);
});

describe('boolean icon resolution', function (): void {
    it('can set `true()` with combined icon and color', function (): void {
        $column = IconColumn::make('active')
            ->true('heroicon-o-star', 'warning');

        expect($column->getTrueIcon())->toBe('heroicon-o-star');
        expect($column->getTrueColor())->toBe('warning');
    });

    it('can set `false()` with combined icon and color', function (): void {
        $column = IconColumn::make('active')
            ->false('heroicon-o-x-circle', 'gray');

        expect($column->getFalseIcon())->toBe('heroicon-o-x-circle');
        expect($column->getFalseColor())->toBe('gray');
    });

    it('can resolve `getIcon()` for true state in boolean mode', function (): void {
        $column = IconColumn::make('active')
            ->boolean();

        $icon = $column->getIcon(true);

        expect($icon)->not->toBeNull();
    });

    it('can resolve `getIcon()` for false state in boolean mode', function (): void {
        $column = IconColumn::make('active')
            ->boolean();

        $icon = $column->getIcon(false);

        expect($icon)->not->toBeNull();
    });

    it('returns fluent `$this` from `icons()`', function (): void {
        $column = IconColumn::make('active');

        $result = $column->icons(['active' => 'heroicon-o-check', 'inactive' => 'heroicon-o-x-mark']);

        expect($result)->toBe($column);
    });
});

describe('URLs', function (): void {
    it('can render a state-based URL', function (): void {
        Post::factory()->create();

        livewire(PostsTableWithIconColumnUrl::class)
            ->assertSeeHtml('href="https://example.com/icon-link"');
    });

    it('can render a static URL', function (): void {
        Post::factory()->create();

        livewire(PostsTableWithIconColumnStaticUrl::class)
            ->assertSeeHtml('href="https://example.com/static-link"');
    });

    it('can render a URL that opens in a new tab', function (): void {
        Post::factory()->create();

        livewire(PostsTableWithIconColumnUrlNewTab::class)
            ->assertSeeHtml('target="_blank"');
    });
});

describe('rendering', function (): void {
    it('can render with `size()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithSize::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `size()` string enum value', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithStringSize::class)->assertSuccessful();
    });

    it('can render with `falseColor()`', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseColor::class)->assertSuccessful();
    });

    it('can render with `trueColor()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueColor::class)->assertSuccessful();
    });

    it('can render with `falseIcon()`', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseIcon::class)->assertSuccessful();
    });

    it('can render with `trueIcon()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueIcon::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `boolean()` set via `Closure`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithClosureBoolean::class)->assertSuccessful();
    });

    it('can render with `true()` combined icon and color', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueCombined::class)->assertSuccessful();
    });

    it('can render with `false()` combined icon and color', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseCombined::class)->assertSuccessful();
    });
});

class TestTableWithIconColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                IconColumn::make('is_published')
                    ->boolean(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithImplicitBooleanIconColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size(IconSize::Small),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithClosureSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size(static fn (): IconSize => IconSize::Large),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithStringSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size('lg'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->falseColor('warning'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->trueColor('info'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->falseIcon('heroicon-o-x-mark'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->trueIcon('heroicon-o-check'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithListWithLineBreaks extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->listWithLineBreaks(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithClosureBoolean extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueCombined extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->true('heroicon-o-star', 'warning'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseCombined extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->false('heroicon-o-x-circle', 'gray'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
