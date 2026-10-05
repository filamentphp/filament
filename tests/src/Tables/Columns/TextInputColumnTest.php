<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders one error-aware text input tooltip with or without a configured hint', function (?string $tooltip): void {
    $post = Post::factory()->create();
    $column = livewire(TestTableWithTextInputColumn::class)->instance()->getTable()->getColumn('rating');
    $html = $column->record($post)->tooltip($tooltip)->toEmbeddedHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)->toContain('error === undefined ? ' . ($tooltip ? '{' : 'false'))
        ->toContain('content: error')
        ->toContain('allowHTML: false');
})->with([null, 'Edit rating']);

it('prioritizes text input errors and restores the configured tooltip after a successful save', function (): void {
    Artisan::call('filament:assets');
    $author = User::factory()->create(['name' => 'Alex Morgan', 'json' => ['display_name' => 'Pending']]);
    Post::factory()->create(['author_id' => $author->getKey()]);
    $this->actingAs(User::factory()->create());
    $selector = '[data-testid="author-name-input"] input:not([type="hidden"])';

    foreach ([false, true] as $isDarkMode) {
        $author->refresh()->update(['json' => ['display_name' => 'Pending']]);
        $page = visit('/columns-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->assertVisible($selector)
            ->assertScript('typeof Alpine.$data(document.querySelector(\'[data-testid="author-name-input"]\')).getServerState === "function"');
        $page->script('document.querySelector(\'' . $selector . '\').focus()');

        $page
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('document.querySelector(\'[role="tooltip"]\').textContent', 'Update name for Alex Morgan')
            ->assertScript('document.activeElement.hasAttribute("aria-describedby")')
            ->fill($selector, 'Rejected')
            ->click('[data-testid="enum-label-column"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-name-input"]\')).error', 'Approval <em>required</em>.')
            ->hover($selector)
            ->assertScript('Array.from(document.querySelectorAll(\'[role="tooltip"]\')).filter(element => getComputedStyle(element).visibility === "visible").map(element => element.textContent)', ['Approval <em>required</em>.'])
            ->assertMissing('[role="tooltip"] em')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['display_name'])->toBe('Pending');

        $page->fill($selector, 'Approved')
            ->click('[data-testid="enum-label-column"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-name-input"]\')).error === undefined')
            ->hover($selector)
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('Array.from(document.querySelectorAll(\'[role="tooltip"]\')).filter(element => getComputedStyle(element).visibility === "visible").map(element => element.textContent)', ['Update name for Alex Morgan'])
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['display_name'])->toBe('Approved');
    }
});

it('injects `$state` and a `null` `$relatedRecord` into update callbacks without a relationship', function (): void {
    $calls = [];

    $column = TextInputColumn::make('name')
        ->record(new User(['name' => 'Original name']))
        ->beforeStateUpdated(static function (string $state, ?User $relatedRecord) use (&$calls): void {
            $calls[] = ['before', $state, $relatedRecord];
        })
        ->updateStateUsing(static function (string $state, ?User $relatedRecord) use (&$calls): string {
            $calls[] = ['update', $state, $relatedRecord];

            return strtoupper($state);
        })
        ->afterStateUpdated(static function (string $state, ?User $relatedRecord) use (&$calls): void {
            $calls[] = ['after', $state, $relatedRecord];
        });

    expect($column->updateState('updated'))->toBe('UPDATED')
        ->and($calls)->toBe([
            ['before', 'updated', null],
            ['update', 'updated', null],
            ['after', 'updated', null],
        ]);
});

it('resolves one relationship model for all update callbacks without cached rendering state', function (): void {
    $author = User::factory()->create(['name' => 'Original name']);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);
    $callbackRelatedRecords = [];

    $column = TextInputColumn::make('author.name')
        ->record($post)
        ->beforeStateUpdated(static function (User $relatedRecord) use (&$callbackRelatedRecords): void {
            $callbackRelatedRecords[] = $relatedRecord;
        })
        ->updateStateUsing(static function (string $state, Post $record, User $relatedRecord) use (&$callbackRelatedRecords, $post): string {
            expect($state)->toBe('Updated name');
            expect($record)->toBe($post);

            $callbackRelatedRecords[] = $relatedRecord;

            return strtoupper($state);
        })
        ->afterStateUpdated(static function (User $relatedRecord) use (&$callbackRelatedRecords): void {
            $callbackRelatedRecords[] = $relatedRecord;
        });

    expect($column->updateState('Updated name'))->toBe('UPDATED NAME')
        ->and($callbackRelatedRecords)->toHaveCount(3)
        ->and($callbackRelatedRecords[0])->toBe($callbackRelatedRecords[1])
        ->and($callbackRelatedRecords[1])->toBe($callbackRelatedRecords[2])
        ->and($callbackRelatedRecords[0]->is($author))->toBeTrue();
});

it('resolves the default relationship persistence target after `beforeStateUpdated()`', function (): void {
    $originalAuthor = User::factory()->create(['name' => 'Original author']);
    $replacementAuthor = User::factory()->create(['name' => 'Replacement author']);
    $post = Post::factory()->create(['author_id' => $originalAuthor->getKey()]);
    $afterRelatedRecord = null;

    $column = TextInputColumn::make('author.name')
        ->record($post)
        ->beforeStateUpdated(static function (User $relatedRecord) use ($post, $replacementAuthor): void {
            expect($relatedRecord->is($post->author))->toBeTrue();

            $post->author()->associate($replacementAuthor);
            $post->save();
        })
        ->afterStateUpdated(static function (User $relatedRecord) use (&$afterRelatedRecord): void {
            $afterRelatedRecord = $relatedRecord;
        });

    expect($column->updateState('Updated replacement'))->toBe('Updated replacement')
        ->and($originalAuthor->refresh()->name)->toBe('Original author')
        ->and($replacementAuthor->refresh()->name)->toBe('Updated replacement')
        ->and($afterRelatedRecord)->toBeInstanceOf(User::class)
        ->and($afterRelatedRecord->is($replacementAuthor))->toBeTrue();
});

it('recomputes a nested `MorphTo` persistence path after `beforeStateUpdated()`', function (): void {
    $team = Team::factory()->create(['name' => 'Original team']);
    $user = User::factory()->create(['team_id' => $team->getKey()]);
    $profile = Profile::factory()->create();
    $image = Image::factory()->for($profile, 'imageable')->create();
    $afterRelatedRecord = null;

    $column = TextInputColumn::make('imageable.team.name')
        ->record($image)
        ->beforeStateUpdated(static function () use ($image, $user): void {
            $image->imageable()->associate($user);
            $image->save();
        })
        ->afterStateUpdated(static function (Team $relatedRecord) use (&$afterRelatedRecord): void {
            $afterRelatedRecord = $relatedRecord;
        });

    expect($column->updateState('Updated team'))->toBe('Updated team')
        ->and($team->refresh()->name)->toBe('Updated team')
        ->and($afterRelatedRecord)->toBeInstanceOf(Team::class)
        ->and($afterRelatedRecord->is($team))->toBeTrue();
});

it('can set `type()` and get with `getType()`', function (): void {
    expect(TextInputColumn::make('rating')->type('number')->getType())->toBe('number');
});

it('defaults `getType()` to `text`', function (): void {
    expect(TextInputColumn::make('rating')->getType())->toBe('text');
});

it('can set `mask()` and get with `getMask()`', function (): void {
    expect(TextInputColumn::make('phone')->mask('999-999-9999')->getMask())->toBe('999-999-9999');
});

it('defaults `getMask()` to `null`', function (): void {
    expect(TextInputColumn::make('rating')->getMask())->toBeNull();
});

it('can set `prefix()` and get with `getPrefixLabel()`', function (): void {
    expect(TextInputColumn::make('price')->prefix('$')->getPrefixLabel())->toBe('$');
});

it('defaults `getPrefixLabel()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getPrefixLabel())->toBeNull();
});

it('can set `suffix()` and get with `getSuffixLabel()`', function (): void {
    expect(TextInputColumn::make('price')->suffix('USD')->getSuffixLabel())->toBe('USD');
});

it('defaults `getSuffixLabel()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getSuffixLabel())->toBeNull();
});

it('can set `inlinePrefix()` and get with `isPrefixInline()`', function (): void {
    expect(TextInputColumn::make('price')->inlinePrefix()->isPrefixInline())->toBeTrue();
});

it('defaults `isPrefixInline()` to `false`', function (): void {
    expect(TextInputColumn::make('price')->isPrefixInline())->toBeFalse();
});

it('can set `inlineSuffix()` and get with `isSuffixInline()`', function (): void {
    expect(TextInputColumn::make('price')->inlineSuffix()->isSuffixInline())->toBeTrue();
});

it('defaults `isSuffixInline()` to `false`', function (): void {
    expect(TextInputColumn::make('price')->isSuffixInline())->toBeFalse();
});

it('can set `prefixIcon()` and get with `getPrefixIcon()`', function (): void {
    expect(TextInputColumn::make('price')->prefixIcon('heroicon-o-currency-dollar')->getPrefixIcon())->toBe('heroicon-o-currency-dollar');
});

it('defaults `getPrefixIcon()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getPrefixIcon())->toBeNull();
});

it('can set `suffixIcon()` and get with `getSuffixIcon()`', function (): void {
    expect(TextInputColumn::make('price')->suffixIcon('heroicon-o-check')->getSuffixIcon())->toBe('heroicon-o-check');
});

it('defaults `getSuffixIcon()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getSuffixIcon())->toBeNull();
});

it('can set `prefixIconColor()` and get with `getPrefixIconColor()`', function (): void {
    expect(TextInputColumn::make('price')->prefixIconColor('success')->getPrefixIconColor())->toBe('success');
});

it('defaults `getPrefixIconColor()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getPrefixIconColor())->toBeNull();
});

it('can set `suffixIconColor()` and get with `getSuffixIconColor()`', function (): void {
    expect(TextInputColumn::make('price')->suffixIconColor('danger')->getSuffixIconColor())->toBe('danger');
});

it('defaults `getSuffixIconColor()` to `null`', function (): void {
    expect(TextInputColumn::make('price')->getSuffixIconColor())->toBeNull();
});

it('can set `type()` with a `Closure`', function (): void {
    expect(TextInputColumn::make('rating')->type(static fn (): string => 'email')->getType())->toBe('email');
});

it('can set `mask()` with a `Closure`', function (): void {
    expect(TextInputColumn::make('phone')->mask(static fn (): string => '(999) 999-9999')->getMask())->toBe('(999) 999-9999');
});

it('can set `mask()` with `RawJs`', function (): void {
    $column = TextInputColumn::make('price')
        ->mask(RawJs::make('$money($input)'));

    expect($column->getMask())->toBeInstanceOf(RawJs::class);
});

it('can set `prefix()` with inline and get `isPrefixInline()`', function (): void {
    $column = TextInputColumn::make('price')
        ->prefix('$', isInline: true);

    expect($column->getPrefixLabel())->toBe('$');
    expect($column->isPrefixInline())->toBeTrue();
});

it('can set `suffix()` with inline and get `isSuffixInline()`', function (): void {
    $column = TextInputColumn::make('price')
        ->suffix('USD', isInline: true);

    expect($column->getSuffixLabel())->toBe('USD');
    expect($column->isSuffixInline())->toBeTrue();
});

it('can set `prefixIcon()` with inline', function (): void {
    $column = TextInputColumn::make('price')
        ->prefixIcon('heroicon-o-dollar', isInline: true);

    expect($column->getPrefixIcon())->toBe('heroicon-o-dollar');
    expect($column->isPrefixInline())->toBeTrue();
});

it('can set `prefix()` with a `Closure`', function (): void {
    expect(TextInputColumn::make('price')->prefix(static fn (): string => '€')->getPrefixLabel())->toBe('€');
});

it('can set `suffix()` with a `Closure`', function (): void {
    expect(TextInputColumn::make('price')->suffix(static fn (): string => 'EUR')->getSuffixLabel())->toBe('EUR');
});

it('can set `prefixIconColor()` with a `Closure`', function (): void {
    expect(TextInputColumn::make('price')->prefixIconColor(static fn (): string => 'info')->getPrefixIconColor())->toBe('info');
});

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithTextInputColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('rating');
});

it('can display different values', function (): void {
    Post::factory()->create(['rating' => 1]);
    Post::factory()->create(['rating' => 5]);
    Post::factory()->create(['rating' => 10]);

    livewire(TestTableWithTextInputColumn::class)
        ->assertSuccessful();
});

describe('rendering', function (): void {
    it('can render with `type()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithType::class)->assertSuccessful();
    });

    it('can render with `type()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureType::class)->assertSuccessful();
    });

    it('can render with `mask()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithMask::class)->assertSuccessful();
    });

    it('can render with `mask()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureMask::class)->assertSuccessful();
    });

    it('can render with `mask()` using `RawJs`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithRawJsMask::class)->assertSuccessful();
    });

    it('can render with `prefix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefix::class)->assertSuccessful();
    });

    it('can render with `prefix()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosurePrefix::class)->assertSuccessful();
    });

    it('can render with `prefix()` inline', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlinePrefix::class)->assertSuccessful();
    });

    it('can render with `suffix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffix::class)->assertSuccessful();
    });

    it('can render with `suffix()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureSuffix::class)->assertSuccessful();
    });

    it('can render with `suffix()` inline', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlineSuffix::class)->assertSuccessful();
    });

    it('can render with `prefixIcon()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefixIcon::class)->assertSuccessful();
    });

    it('can render with `suffixIcon()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffixIcon::class)->assertSuccessful();
    });

    it('can render with `prefixIconColor()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefixIconColor::class)->assertSuccessful();
    });

    it('can render with `prefixIconColor()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosurePrefixIconColor::class)->assertSuccessful();
    });

    it('can render with `suffixIconColor()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffixIconColor::class)->assertSuccessful();
    });

    it('can render with `inlinePrefix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlinePrefixMethod::class)->assertSuccessful();
    });

    it('can render with `inlineSuffix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlineSuffixMethod::class)->assertSuccessful();
    });
});

class TestTableWithTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                TextInputColumn::make('rating'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithType extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->type('number'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureType extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->type(static fn (): string => 'email'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask('999-999-9999'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask(static fn (): string => '(999) 999-9999'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithRawJsMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask(RawJs::make('$money($input)')),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosurePrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix(static fn (): string => '€'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlinePrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$', isInline: true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix(static fn (): string => 'EUR'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlineSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD', isInline: true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefixIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffixIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffixIcon('heroicon-o-check'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar')->prefixIconColor('success'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosurePrefixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar')->prefixIconColor(static fn (): string => 'info'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffixIcon('heroicon-o-check')->suffixIconColor('danger'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlinePrefixMethod extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$')->inlinePrefix(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlineSuffixMethod extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD')->inlineSuffix(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
