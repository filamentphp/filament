<?php

use Filament\Schemas\Components\Image;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with a URL and alt text', function (): void {
    $image = Image::make('https://example.com/photo.jpg', 'A photo');

    expect($image->getUrl())->toBe('https://example.com/photo.jpg');
    expect($image->getAlt())->toBe('A photo');
});

it('can set `url()` with a `Closure`', function (): void {
    $image = Image::make('initial', 'alt')
        ->url(static fn (): string => 'https://example.com/dynamic.jpg');

    expect($image->getUrl())->toBe('https://example.com/dynamic.jpg');
});

it('can set `alt()` with a `Closure`', function (): void {
    $image = Image::make('https://example.com/photo.jpg', 'initial')
        ->alt(static fn (): string => 'Dynamic alt');

    expect($image->getAlt())->toBe('Dynamic alt');
});

describe('image dimensions', function (): void {
    it('returns `null` for `getImageHeight()` by default', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt');

        expect($image->getImageHeight())->toBeNull();
    });

    it('returns `null` for `getImageWidth()` by default', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt');

        expect($image->getImageWidth())->toBeNull();
    });

    it('can set `imageHeight()` with an int and returns px string', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageHeight(200);

        expect($image->getImageHeight())->toBe('200px');
    });

    it('can set `imageHeight()` with a string', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageHeight('10rem');

        expect($image->getImageHeight())->toBe('10rem');
    });

    it('can set `imageHeight()` with a `Closure`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageHeight(static fn (): int => 150);

        expect($image->getImageHeight())->toBe('150px');
    });

    it('can set `imageWidth()` with an int and returns px string', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageWidth(300);

        expect($image->getImageWidth())->toBe('300px');
    });

    it('can set `imageWidth()` with a string', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageWidth('50%');

        expect($image->getImageWidth())->toBe('50%');
    });

    it('can set `imageWidth()` with a `Closure`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageWidth(static fn (): string => '20rem');

        expect($image->getImageWidth())->toBe('20rem');
    });

    it('can set `imageSize()` to set both width and height', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageSize(100);

        expect($image->getImageWidth())->toBe('100px');
        expect($image->getImageHeight())->toBe('100px');
    });

    it('can clear `imageHeight()` with `null`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageHeight(200)
            ->imageHeight(null);

        expect($image->getImageHeight())->toBeNull();
    });

    it('can clear `imageWidth()` with `null`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->imageWidth(300)
            ->imageWidth(null);

        expect($image->getImageWidth())->toBeNull();
    });
});

describe('alignment', function (): void {
    it('returns `null` for `getAlignment()` by default', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt');

        expect($image->getAlignment())->toBeNull();
    });

    it('can set `alignment()` with an `Alignment` enum', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignment(Alignment::Center);

        expect($image->getAlignment())->toBe(Alignment::Center);
    });

    it('can set `alignment()` with a string that maps to an enum', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignment('center');

        expect($image->getAlignment())->toBe(Alignment::Center);
    });

    it('can set `alignment()` with a `Closure`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignment(static fn (): Alignment => Alignment::End);

        expect($image->getAlignment())->toBe(Alignment::End);
    });

    it('can use `alignStart()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignStart();

        expect($image->getAlignment())->toBe(Alignment::Start);
    });

    it('can use `alignCenter()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignCenter();

        expect($image->getAlignment())->toBe(Alignment::Center);
    });

    it('can use `alignEnd()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignEnd();

        expect($image->getAlignment())->toBe(Alignment::End);
    });

    it('can use `alignJustify()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignJustify();

        expect($image->getAlignment())->toBe(Alignment::Justify);
    });

    it('can use `alignLeft()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignLeft();

        expect($image->getAlignment())->toBe(Alignment::Left);
    });

    it('can use `alignRight()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignRight();

        expect($image->getAlignment())->toBe(Alignment::Right);
    });

    it('returns `null` from `alignStart()` when condition is `false`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->alignStart(false);

        expect($image->getAlignment())->toBeNull();
    });
});

describe('tooltip', function (): void {
    it('returns `null` for `getTooltip()` by default', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt');

        expect($image->getTooltip())->toBeNull();
    });

    it('can set `tooltip()`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->tooltip('Click to enlarge');

        expect($image->getTooltip())->toBe('Click to enlarge');
    });

    it('can set `tooltip()` with a `Closure`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->tooltip(static fn (): string => 'Dynamic tooltip');

        expect($image->getTooltip())->toBe('Dynamic tooltip');
    });

    it('can clear `tooltip()` with `null`', function (): void {
        $image = Image::make('https://example.com/photo.jpg', 'alt')
            ->tooltip('Tip')
            ->tooltip(null);

        expect($image->getTooltip())->toBeNull();
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderImage::class)->assertSuccessful();
    });

    it('can render with `url()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureUrl::class)->assertSuccessful();
    });

    it('can render with `alt()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureAlt::class)->assertSuccessful();
    });

    it('can render with `imageHeight()` int', function (): void {
        livewire(RenderImageWithHeightInt::class)->assertSuccessful();
    });

    it('can render with `imageHeight()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureHeight::class)->assertSuccessful();
    });

    it('can render with `imageWidth()` int', function (): void {
        livewire(RenderImageWithWidthInt::class)->assertSuccessful();
    });

    it('can render with `imageWidth()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureWidth::class)->assertSuccessful();
    });

    it('can render with `imageSize()`', function (): void {
        livewire(RenderImageWithSize::class)->assertSuccessful();
    });

    it('can render with `alignment()`', function (): void {
        livewire(RenderImageWithAlignment::class)->assertSuccessful();
    });

    it('can render with `alignment()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureAlignment::class)->assertSuccessful();
    });

    it('can render with `tooltip()`', function (): void {
        livewire(RenderImageWithTooltip::class)->assertSuccessful();
    });

    it('can render with `tooltip()` set via `Closure`', function (): void {
        livewire(RenderImageWithClosureTooltip::class)->assertSuccessful();
    });
});

class RenderImage extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'A photo')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureUrl extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('initial', 'alt')->url(static fn (): string => 'https://example.com/dynamic.jpg')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureAlt extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'initial')->alt(static fn (): string => 'Dynamic alt')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithHeightInt extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->imageHeight(200)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureHeight extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->imageHeight(static fn (): int => 150)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithWidthInt extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->imageWidth(300)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureWidth extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->imageWidth(static fn (): string => '20rem')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->imageSize(100)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->alignment(Alignment::Center)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->alignment(static fn (): Alignment => Alignment::End)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithTooltip extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->tooltip('Click to enlarge')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderImageWithClosureTooltip extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Image::make('https://example.com/photo.jpg', 'alt')->tooltip(static fn (): string => 'Dynamic tooltip')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
