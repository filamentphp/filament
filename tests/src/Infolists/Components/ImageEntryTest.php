<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithImageEntry::class)
        ->assertSuccessful()
        ->assertSeeHtml('src="https://example.com/image.jpg"');
});

it('escapes `"` in `src`', function (): void {
    livewire(TestComponentWithQuoteInImageEntry::class)
        ->assertSuccessful()
        ->assertSeeHtml('data:image/png,&quot;');
});

it('can render with circular style', function (): void {
    livewire(TestComponentWithCircularImageEntry::class)
        ->assertSuccessful()
        ->assertSeeHtml('src="https://example.com/avatar.jpg"');
});

it('can set `circular()`', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->isCircular())->toBeFalse();
    $entry->circular();
    expect($entry->isCircular())->toBeTrue();
});

it('can set `square()`', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->isSquare())->toBeFalse();
    $entry->square();
    expect($entry->isSquare())->toBeTrue();
});

it('can set `imageSize()`', function (): void {
    $entry = ImageEntry::make('image')->imageSize(100);
    expect($entry->getImageWidth())->toBe('100px');
    expect($entry->getImageHeight())->toBe('100px');
});

it('can set `imageWidth()` and `imageHeight()` independently', function (): void {
    $entry = ImageEntry::make('image')
        ->imageWidth(200)
        ->imageHeight(150);
    expect($entry->getImageWidth())->toBe('200px');
    expect($entry->getImageHeight())->toBe('150px');
});

it('can set `defaultImageUrl()`', function (): void {
    $entry = ImageEntry::make('image')->defaultImageUrl('https://example.com/default.jpg');
    expect($entry->getDefaultImageUrl())->toBe('https://example.com/default.jpg');
});

it('returns `null` for `getDefaultImageUrl()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getDefaultImageUrl())->toBeNull();
});

it('can set `limit()`', function (): void {
    $entry = ImageEntry::make('image')->limit(5);
    expect($entry->getLimit())->toBe(5);
});

it('returns `null` for `getLimit()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getLimit())->toBeNull();
});

it('can set `checkFileExistence()`', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->shouldCheckFileExistence())->toBeTrue();
    $entry->checkFileExistence(false);
    expect($entry->shouldCheckFileExistence())->toBeFalse();
});

it('can set `stacked()`', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->isStacked())->toBeFalse();
    $entry->stacked();
    expect($entry->isStacked())->toBeTrue();
});

it('can set `overlap()`', function (): void {
    $entry = ImageEntry::make('image')->overlap(3);
    expect($entry->getOverlap())->toBe(3);
});

it('returns `null` for `getOverlap()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getOverlap())->toBeNull();
});

it('can set `ring()`', function (): void {
    $entry = ImageEntry::make('image')->ring(2);
    expect($entry->getRing())->toBe(2);
});

it('returns `null` for `getRing()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getRing())->toBeNull();
});

it('can set `limitedRemainingText()`', function (): void {
    $entry = ImageEntry::make('image')->limitedRemainingText();
    expect($entry->hasLimitedRemainingText())->toBeTrue();
});

it('`hasLimitedRemainingText()` returns `false` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->hasLimitedRemainingText())->toBeFalse();
});

it('can set `limitedRemainingTextSeparate()`', function (): void {
    $entry = ImageEntry::make('image')->limitedRemainingTextSeparate();
    expect($entry->isLimitedRemainingTextSeparate())->toBeTrue();
});

it('`isLimitedRemainingTextSeparate()` returns `false` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->isLimitedRemainingTextSeparate())->toBeFalse();
});

it('can set `limitedRemainingTextSize()`', function (): void {
    $entry = ImageEntry::make('image')->limitedRemainingTextSize(TextSize::Large);
    expect($entry->getLimitedRemainingTextSize())->toBe(TextSize::Large);
});

it('returns `null` for `getLimitedRemainingTextSize()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getLimitedRemainingTextSize())->toBeNull();
});

it('can set `visibility()`', function (): void {
    $entry = ImageEntry::make('image')->visibility('private');
    expect($entry->getCustomVisibility())->toBe('private');
});

it('returns `null` for `getCustomVisibility()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getCustomVisibility())->toBeNull();
});

it('can set `extraImgAttributes()`', function (): void {
    $entry = ImageEntry::make('image')->extraImgAttributes(['alt' => 'A photo']);
    expect($entry->getExtraImgAttributes())->toBe(['alt' => 'A photo']);
});

it('returns `[]` for `getExtraImgAttributes()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getExtraImgAttributes())->toBe([]);
});

it('`imageHeight()` accepts a string value', function (): void {
    $entry = ImageEntry::make('image')->imageHeight('4rem');
    expect($entry->getImageHeight())->toBe('4rem');
});

it('`imageWidth()` accepts a string value', function (): void {
    $entry = ImageEntry::make('image')->imageWidth('4rem');
    expect($entry->getImageWidth())->toBe('4rem');
});

it('returns `null` for `getImageHeight()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getImageHeight())->toBeNull();
});

it('returns `null` for `getImageWidth()` by default', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->getImageWidth())->toBeNull();
});

it('returns `true` for `canWrapByDefault()`', function (): void {
    $entry = ImageEntry::make('image');
    expect($entry->canWrapByDefault())->toBeTrue();
});

it('can set `circular()` with a `Closure`', function (): void {
    expect(ImageEntry::make('photo')->circular(static fn (): bool => true)->isCircular())->toBeTrue();
});

it('can undo `circular()` with `false`', function (): void {
    expect(ImageEntry::make('photo')->circular()->circular(false)->isCircular())->toBeFalse();
});

it('defaults `isCircular()` to `false`', function (): void {
    expect(ImageEntry::make('photo')->isCircular())->toBeFalse();
});

it('defaults `isSquare()` to `false`', function (): void {
    expect(ImageEntry::make('photo')->isSquare())->toBeFalse();
});

it('can set `stacked()` with a `Closure`', function (): void {
    expect(ImageEntry::make('photos')->stacked(static fn (): bool => true)->isStacked())->toBeTrue();
});

it('can set `limit()` with a `Closure`', function (): void {
    expect(ImageEntry::make('photos')->limit(static fn (): int => 10)->getLimit())->toBe(10);
});

it('uses `3` as default limit for `limit()` when called without argument', function (): void {
    expect(ImageEntry::make('photos')->limit()->getLimit())->toBe(3);
});

it('can set `overlap()` with a `Closure`', function (): void {
    expect(ImageEntry::make('photos')->overlap(static fn (): int => 4)->getOverlap())->toBe(4);
});

it('can set `checkFileExistence()` with a `Closure`', function (): void {
    expect(ImageEntry::make('photo')->checkFileExistence(static fn (): bool => false)->shouldCheckFileExistence())->toBeFalse();
});

it('defaults `isStacked()` to `false`', function (): void {
    expect(ImageEntry::make('photos')->isStacked())->toBeFalse();
});

describe('Closure support', function (): void {
    it('can set `disk()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->disk(static fn (): string => 's3');

        expect($entry->getDiskName())->toBe('s3');
    });

    it('can set `imageHeight()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->imageHeight(static fn (): string => '200px');

        expect($entry->getImageHeight())->toBe('200px');
    });

    it('can set `imageWidth()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->imageWidth(static fn (): string => '300px');

        expect($entry->getImageWidth())->toBe('300px');
    });

    it('can set `square()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->square(static fn (): bool => true);

        expect($entry->isSquare())->toBeTrue();
    });

    it('can set `visibility()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->visibility(static fn (): string => 'private');

        expect($entry->getCustomVisibility())->toBe('private');
    });

    it('can set `defaultImageUrl()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->defaultImageUrl(static fn (): string => 'https://example.com/default.png');

        expect($entry->getDefaultImageUrl())->toBe('https://example.com/default.png');
    });

    it('can set `ring()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photos')
            ->ring(static fn (): int => 3);

        expect($entry->getRing())->toBe(3);
    });

    it('can set `extraImgAttributes()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photo')
            ->extraImgAttributes(static fn (): array => ['loading' => 'lazy']);

        expect($entry->getExtraImgAttributes())->toBe(['loading' => 'lazy']);
    });

    it('can set `limitedRemainingText()` with a `Closure`', function (): void {
        $entry = ImageEntry::make('photos')
            ->limitedRemainingText(static fn (): bool => true);

        expect($entry->hasLimitedRemainingText())->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('can render with `square()`', function (): void {
        livewire(RenderImageEntryWithSquare::class)->assertSuccessful();
    });

    it('can render with `square()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureSquare::class)->assertSuccessful();
    });

    it('can render with `stacked()`', function (): void {
        livewire(RenderImageEntryWithStacked::class)->assertSuccessful();
    });

    it('can render with `stacked()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureStacked::class)->assertSuccessful();
    });

    it('can render with `overlap()`', function (): void {
        livewire(RenderImageEntryWithOverlap::class)->assertSuccessful();
    });

    it('can render with `overlap()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureOverlap::class)->assertSuccessful();
    });

    it('can render with `ring()`', function (): void {
        livewire(RenderImageEntryWithRing::class)->assertSuccessful();
    });

    it('can render with `ring()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureRing::class)->assertSuccessful();
    });

    it('can render with `limit()`', function (): void {
        livewire(RenderImageEntryWithLimit::class)->assertSuccessful();
    });

    it('can render with `limit()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureLimit::class)->assertSuccessful();
    });

    it('can render with `imageSize()`', function (): void {
        livewire(RenderImageEntryWithImageSize::class)->assertSuccessful();
    });

    it('can render with `imageWidth()` and `imageHeight()`', function (): void {
        livewire(RenderImageEntryWithWidthHeight::class)->assertSuccessful();
    });

    it('can render with `imageWidth()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureWidth::class)->assertSuccessful();
    });

    it('can render with `imageHeight()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureHeight::class)->assertSuccessful();
    });

    it('can render with `defaultImageUrl()`', function (): void {
        livewire(RenderImageEntryWithDefaultImageUrl::class)->assertSuccessful();
    });

    it('can render with `defaultImageUrl()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureDefaultImageUrl::class)->assertSuccessful();
    });

    it('can render with `extraImgAttributes()`', function (): void {
        livewire(RenderImageEntryWithExtraImgAttributes::class)->assertSuccessful();
    });

    it('can render with `extraImgAttributes()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureExtraImgAttributes::class)->assertSuccessful();
    });

    it('can render with `checkFileExistence(false)`', function (): void {
        livewire(RenderImageEntryWithNoCheckFileExistence::class)->assertSuccessful();
    });

    it('can render with `checkFileExistence()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureCheckFileExistence::class)->assertSuccessful();
    });

    it('can render with `limitedRemainingText()`', function (): void {
        livewire(RenderImageEntryWithLimitedRemainingText::class)->assertSuccessful();
    });

    it('can render with `limitedRemainingText()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureLimitedRemainingText::class)->assertSuccessful();
    });

    it('can render with `circular()` set via `Closure`', function (): void {
        livewire(RenderImageEntryWithClosureCircular::class)->assertSuccessful();
    });
});

class TestComponentWithImageEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'image' => 'https://example.com/image.jpg',
            ])
            ->components([
                ImageEntry::make('image'),
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

class TestComponentWithQuoteInImageEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'image' => 'data:image/png,"',
            ])
            ->components([
                ImageEntry::make('image'),
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

class TestComponentWithCircularImageEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'avatar' => 'https://example.com/avatar.jpg',
            ])
            ->components([
                ImageEntry::make('avatar')
                    ->circular(),
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

class RenderImageEntryWithSquare extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->square()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureSquare extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->square(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithStacked extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureStacked extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithOverlap extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked()->overlap(3)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureOverlap extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked()->overlap(static fn (): int => 4)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithRing extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked()->ring(2)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureRing extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->stacked()->ring(static fn (): int => 3)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithLimit extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->limit(5)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureLimit extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->limit(static fn (): int => 10)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithImageSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->imageSize(100)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithWidthHeight extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->imageWidth(200)->imageHeight(150)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureWidth extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->imageWidth(static fn (): string => '300px')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureHeight extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->imageHeight(static fn (): string => '200px')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithDefaultImageUrl extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => null])->components([ImageEntry::make('img')->defaultImageUrl('https://example.com/default.jpg')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureDefaultImageUrl extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => null])->components([ImageEntry::make('img')->defaultImageUrl(static fn (): string => 'https://example.com/default.png')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithExtraImgAttributes extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->extraImgAttributes(['alt' => 'A photo'])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureExtraImgAttributes extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->extraImgAttributes(static fn (): array => ['loading' => 'lazy'])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithNoCheckFileExistence extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->checkFileExistence(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureCheckFileExistence extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->checkFileExistence(static fn (): bool => false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithLimitedRemainingText extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->limit(1)->limitedRemainingText()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureLimitedRemainingText extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->limit(1)->limitedRemainingText(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageEntryWithClosureCircular extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['img' => 'https://example.com/photo.jpg'])->components([ImageEntry::make('img')->circular(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
