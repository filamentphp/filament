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
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

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
