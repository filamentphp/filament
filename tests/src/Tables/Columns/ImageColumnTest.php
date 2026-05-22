<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `disk()` and get with `getDiskName()`', function (): void {
    expect(ImageColumn::make('avatar')->disk('s3')->getDiskName())->toBe('s3');
});

it('can set `imageHeight()` and get with `getImageHeight()`', function (): void {
    expect(ImageColumn::make('avatar')->imageHeight(100)->getImageHeight())->toBe('100px');
});

it('can set `imageHeight()` with string and get with `getImageHeight()`', function (): void {
    expect(ImageColumn::make('avatar')->imageHeight('4rem')->getImageHeight())->toBe('4rem');
});

it('defaults `getImageHeight()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getImageHeight())->toBeNull();
});

it('can set `imageWidth()` and get with `getImageWidth()`', function (): void {
    expect(ImageColumn::make('avatar')->imageWidth(80)->getImageWidth())->toBe('80px');
});

it('defaults `getImageWidth()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getImageWidth())->toBeNull();
});

it('can set `circular()` and get with `isCircular()`', function (): void {
    expect(ImageColumn::make('avatar')->circular()->isCircular())->toBeTrue();
});

it('defaults `isCircular()` to `false`', function (): void {
    expect(ImageColumn::make('avatar')->isCircular())->toBeFalse();
});

it('can set `square()` and get with `isSquare()`', function (): void {
    expect(ImageColumn::make('avatar')->square()->isSquare())->toBeTrue();
});

it('defaults `isSquare()` to `false`', function (): void {
    expect(ImageColumn::make('avatar')->isSquare())->toBeFalse();
});

it('can set `defaultImageUrl()` and get with `getDefaultImageUrl()`', function (): void {
    expect(ImageColumn::make('avatar')->defaultImageUrl('https://example.com/default.jpg')->getDefaultImageUrl())->toBe('https://example.com/default.jpg');
});

it('defaults `getDefaultImageUrl()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getDefaultImageUrl())->toBeNull();
});

it('can set `stacked()` and get with `isStacked()`', function (): void {
    expect(ImageColumn::make('avatar')->stacked()->isStacked())->toBeTrue();
});

it('defaults `isStacked()` to `false`', function (): void {
    expect(ImageColumn::make('avatar')->isStacked())->toBeFalse();
});

it('can set `overlap()` and get with `getOverlap()`', function (): void {
    expect(ImageColumn::make('avatar')->overlap(3)->getOverlap())->toBe(3);
});

it('defaults `getOverlap()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getOverlap())->toBeNull();
});

it('can set `ring()` and get with `getRing()`', function (): void {
    expect(ImageColumn::make('avatar')->ring(2)->getRing())->toBe(2);
});

it('defaults `getRing()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getRing())->toBeNull();
});

it('can set `limit()` and get with `getLimit()`', function (): void {
    expect(ImageColumn::make('avatar')->limit(5)->getLimit())->toBe(5);
});

it('defaults `getLimit()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getLimit())->toBeNull();
});

it('can set `limitedRemainingText()` and get with `hasLimitedRemainingText()`', function (): void {
    expect(ImageColumn::make('avatar')->limitedRemainingText()->hasLimitedRemainingText())->toBeTrue();
});

it('defaults `hasLimitedRemainingText()` to `false`', function (): void {
    expect(ImageColumn::make('avatar')->hasLimitedRemainingText())->toBeFalse();
});

it('can set `checkFileExistence()` to `false` and get with `shouldCheckFileExistence()`', function (): void {
    expect(ImageColumn::make('avatar')->checkFileExistence(false)->shouldCheckFileExistence())->toBeFalse();
});

it('defaults `shouldCheckFileExistence()` to `true`', function (): void {
    expect(ImageColumn::make('avatar')->shouldCheckFileExistence())->toBeTrue();
});

it('can set `visibility()` and get with `getCustomVisibility()`', function (): void {
    expect(ImageColumn::make('avatar')->visibility('private')->getCustomVisibility())->toBe('private');
});

it('can set `circular()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->circular(static fn (): bool => true)->isCircular())->toBeTrue();
});

it('can undo `circular()` with `false`', function (): void {
    expect(ImageColumn::make('avatar')->circular()->circular(false)->isCircular())->toBeFalse();
});

it('can set `stacked()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->stacked(static fn (): bool => true)->isStacked())->toBeTrue();
});

it('can set `overlap()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->overlap(static fn (): int => 5)->getOverlap())->toBe(5);
});

it('can set `ring()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->ring(static fn (): int => 4)->getRing())->toBe(4);
});

it('can set `limit()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->limit(static fn (): int => 10)->getLimit())->toBe(10);
});

it('uses `3` as default limit for `limit()` when called without argument', function (): void {
    expect(ImageColumn::make('avatar')->limit()->getLimit())->toBe(3);
});

it('can set `imageSize()` and it sets both height and width', function (): void {
    $column = ImageColumn::make('avatar')->imageSize(64);

    expect($column->getImageHeight())->toBe('64px');
    expect($column->getImageWidth())->toBe('64px');
});

it('can set `extraImgAttributes()`', function (): void {
    $column = ImageColumn::make('avatar')
        ->extraImgAttributes(['loading' => 'lazy', 'alt' => 'User avatar']);

    $attributes = $column->getExtraImgAttributes();

    expect($attributes)->toHaveKey('loading');
    expect($attributes['loading'])->toBe('lazy');
});

it('can set `limitedRemainingTextSize()` and get with `getLimitedRemainingTextSize()`', function (): void {
    expect(ImageColumn::make('avatar')->limitedRemainingTextSize('sm')->getLimitedRemainingTextSize())->not->toBeNull();
});

it('defaults `getLimitedRemainingTextSize()` to `null`', function (): void {
    expect(ImageColumn::make('avatar')->getLimitedRemainingTextSize())->toBeNull();
});

it('can set `checkFileExistence()` with a `Closure`', function (): void {
    expect(ImageColumn::make('avatar')->checkFileExistence(static fn (): bool => false)->shouldCheckFileExistence())->toBeFalse();
});

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithImageColumn::class)
        ->assertSuccessful();
});

it('escapes `"` in `src`', function (): void {
    Post::factory()->create([
        'title' => 'data:image/png,"',
    ]);

    livewire(TestTableWithImageColumn::class)
        ->assertSuccessful()
        ->assertSeeHtml('data:image/png,&quot;');
});

describe('rendering', function (): void {
    it('can render with `circular()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithCircular::class)->assertSuccessful();
    });

    it('can render with `circular()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureCircular::class)->assertSuccessful();
    });

    it('can render with `square()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithSquare::class)->assertSuccessful();
    });

    it('can render with `stacked()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithStacked::class)->assertSuccessful();
    });

    it('can render with `stacked()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureStacked::class)->assertSuccessful();
    });

    it('can render with `overlap()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithOverlap::class)->assertSuccessful();
    });

    it('can render with `overlap()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureOverlap::class)->assertSuccessful();
    });

    it('can render with `ring()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithRing::class)->assertSuccessful();
    });

    it('can render with `ring()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureRing::class)->assertSuccessful();
    });

    it('can render with `limit()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithLimit::class)->assertSuccessful();
    });

    it('can render with `limit()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureLimit::class)->assertSuccessful();
    });

    it('can render with `limitedRemainingText()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithLimitedRemainingText::class)->assertSuccessful();
    });

    it('can render with `imageHeight()` as int', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithImageHeightInt::class)->assertSuccessful();
    });

    it('can render with `imageHeight()` as string', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithImageHeightString::class)->assertSuccessful();
    });

    it('can render with `imageWidth()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithImageWidth::class)->assertSuccessful();
    });

    it('can render with `imageSize()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithImageSize::class)->assertSuccessful();
    });

    it('can render with `defaultImageUrl()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithDefaultImageUrl::class)->assertSuccessful();
    });

    it('can render with `extraImgAttributes()`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithExtraImgAttributes::class)->assertSuccessful();
    });

    it('can render with `checkFileExistence(false)`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithNoCheckFileExistence::class)->assertSuccessful();
    });

    it('can render with `checkFileExistence()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderImageColumnWithClosureCheckFileExistence::class)->assertSuccessful();
    });
});

class TestTableWithImageColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                ImageColumn::make('title'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithCircular extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->circular(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureCircular extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->circular(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithSquare extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->square(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithStacked extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureStacked extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithOverlap extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked()->overlap(3),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureOverlap extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked()->overlap(static fn (): int => 5),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithRing extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked()->ring(2),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureRing extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->stacked()->ring(static fn (): int => 4),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithLimit extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->limit(5),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureLimit extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->limit(static fn (): int => 10),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithLimitedRemainingText extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->limit(1)->limitedRemainingText(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithImageHeightInt extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->imageHeight(100),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithImageHeightString extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->imageHeight('4rem'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithImageWidth extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->imageWidth(80),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithImageSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->imageSize(64),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithDefaultImageUrl extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->defaultImageUrl('https://example.com/default.jpg'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithExtraImgAttributes extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->extraImgAttributes(['loading' => 'lazy']),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithNoCheckFileExistence extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->checkFileExistence(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderImageColumnWithClosureCheckFileExistence extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            ImageColumn::make('title')->checkFileExistence(static fn (): bool => false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
