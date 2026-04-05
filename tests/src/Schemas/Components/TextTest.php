<?php

use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\JsContent;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with a string', function (): void {
    $text = Text::make('Hello world');

    expect($text->getContent())->toBe('Hello world');
});

it('can be constructed with `null`', function (): void {
    $text = Text::make(null);

    expect($text->getContent())->toBeNull();
});

it('can set `content()` with a `Closure`', function (): void {
    $text = Text::make(null)
        ->content(static fn (): string => 'Dynamic');

    expect($text->getContent())->toBe('Dynamic');
});

it('can set `content()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<strong>Bold</strong>');
    $text = Text::make($htmlable);

    expect($text->getContent())->toBe($htmlable);
});

describe('badge', function (): void {
    it('defaults `isBadge()` to `false`', function (): void {
        $text = Text::make('Test');

        expect($text->isBadge())->toBeFalse();
    });

    it('can set `badge()`', function (): void {
        $text = Text::make('Test')->badge();

        expect($text->isBadge())->toBeTrue();
    });

    it('can set `badge()` to `false`', function (): void {
        $text = Text::make('Test')->badge()->badge(false);

        expect($text->isBadge())->toBeFalse();
    });

    it('can set `badge()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->badge(static fn (): bool => true);

        expect($text->isBadge())->toBeTrue();
    });
});

describe('size', function (): void {
    it('returns `null` for `getSize()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getSize())->toBeNull();
    });

    it('can set `size()` with a `TextSize` enum', function (): void {
        $text = Text::make('Test')->size(TextSize::Large);

        expect($text->getSize())->toBe(TextSize::Large);
    });

    it('can set `size()` with a string that maps to a `TextSize` enum', function (): void {
        $text = Text::make('Test')->size('lg');

        expect($text->getSize())->toBe(TextSize::Large);
    });

    it('can set `size()` with a custom string that does not map to an enum', function (): void {
        $text = Text::make('Test')->size('custom');

        expect($text->getSize())->toBe('custom');
    });

    it('can set `size()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->size(static fn (): TextSize => TextSize::Small);

        expect($text->getSize())->toBe(TextSize::Small);
    });

    it('can clear `size()` with `null`', function (): void {
        $text = Text::make('Test')
            ->size(TextSize::Large)
            ->size(null);

        expect($text->getSize())->toBeNull();
    });
});

describe('JavaScript content', function (): void {
    it('can convert content to `JsContent` with `js()`', function (): void {
        $text = Text::make('someJsExpression');
        $text->js();

        expect($text->getContent())->toBeInstanceOf(JsContent::class);
    });
});

describe('color', function (): void {
    it('defaults `getColor()` to `gray` via `defaultColor()`', function (): void {
        $text = Text::make('Test');

        expect($text->getColor())->toBe('gray');
    });

    it('can set `color()`', function (): void {
        $text = Text::make('Test')->color('danger');

        expect($text->getColor())->toBe('danger');
    });

    it('can set `color()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->color(static fn (): string => 'success');

        expect($text->getColor())->toBe('success');
    });

    it('can clear `color()` with `null` to fall back to default', function (): void {
        $text = Text::make('Test')
            ->color('danger')
            ->color(null);

        expect($text->getColor())->toBe('gray');
    });
});

describe('font family', function (): void {
    it('returns `null` for `getFontFamily()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getFontFamily())->toBeNull();
    });

    it('can set `fontFamily()` with a `FontFamily` enum', function (): void {
        $text = Text::make('Test')->fontFamily(FontFamily::Mono);

        expect($text->getFontFamily())->toBe(FontFamily::Mono);
    });

    it('can set `fontFamily()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->fontFamily(static fn (): FontFamily => FontFamily::Mono);

        expect($text->getFontFamily())->toBe(FontFamily::Mono);
    });

    it('can clear `fontFamily()` with `null`', function (): void {
        $text = Text::make('Test')
            ->fontFamily(FontFamily::Mono)
            ->fontFamily(null);

        expect($text->getFontFamily())->toBeNull();
    });
});

describe('weight', function (): void {
    it('returns `null` for `getWeight()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getWeight())->toBeNull();
    });

    it('can set `weight()` with a `FontWeight` enum', function (): void {
        $text = Text::make('Test')->weight(FontWeight::Bold);

        expect($text->getWeight())->toBe(FontWeight::Bold);
    });

    it('can set `weight()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->weight(static fn (): FontWeight => FontWeight::SemiBold);

        expect($text->getWeight())->toBe(FontWeight::SemiBold);
    });

    it('can clear `weight()` with `null`', function (): void {
        $text = Text::make('Test')
            ->weight(FontWeight::Bold)
            ->weight(null);

        expect($text->getWeight())->toBeNull();
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getIcon())->toBeNull();
    });

    it('can set `icon()` with a string', function (): void {
        $text = Text::make('Test')->icon('heroicon-o-check');

        expect($text->getIcon())->toBe('heroicon-o-check');
    });

    it('can set `icon()` with a `BackedEnum`', function (): void {
        $text = Text::make('Test')->icon(Heroicon::Check);

        expect($text->getIcon())->toBe(Heroicon::Check);
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->icon(static fn (): string => 'heroicon-o-star');

        expect($text->getIcon())->toBe('heroicon-o-star');
    });

    it('can clear `icon()` with `null`', function (): void {
        $text = Text::make('Test')
            ->icon('heroicon-o-check')
            ->icon(null);

        expect($text->getIcon())->toBeNull();
    });
});

describe('icon position', function (): void {
    it('defaults `getIconPosition()` to `Before`', function (): void {
        $text = Text::make('Test');

        expect($text->getIconPosition())->toBe(IconPosition::Before);
    });

    it('can set `iconPosition()`', function (): void {
        $text = Text::make('Test')->iconPosition(IconPosition::After);

        expect($text->getIconPosition())->toBe(IconPosition::After);
    });

    it('can set `iconPosition()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->iconPosition(static fn (): IconPosition => IconPosition::After);

        expect($text->getIconPosition())->toBe(IconPosition::After);
    });
});

describe('icon size', function (): void {
    it('returns `null` for `getIconSize()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getIconSize())->toBeNull();
    });

    it('can set `iconSize()` with an enum', function (): void {
        $text = Text::make('Test')->iconSize(IconSize::Large);

        expect($text->getIconSize())->toBe(IconSize::Large);
    });

    it('can set `iconSize()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->iconSize(static fn (): IconSize => IconSize::Small);

        expect($text->getIconSize())->toBe(IconSize::Small);
    });

    it('can clear `iconSize()` with `null`', function (): void {
        $text = Text::make('Test')
            ->iconSize(IconSize::Large)
            ->iconSize(null);

        expect($text->getIconSize())->toBeNull();
    });
});

describe('tooltip', function (): void {
    it('returns `null` for `getTooltip()` by default', function (): void {
        $text = Text::make('Test');

        expect($text->getTooltip())->toBeNull();
    });

    it('can set `tooltip()`', function (): void {
        $text = Text::make('Test')->tooltip('More info');

        expect($text->getTooltip())->toBe('More info');
    });

    it('can set `tooltip()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->tooltip(static fn (): string => 'Dynamic tip');

        expect($text->getTooltip())->toBe('Dynamic tip');
    });

    it('can clear `tooltip()` with `null`', function (): void {
        $text = Text::make('Test')
            ->tooltip('Tip')
            ->tooltip(null);

        expect($text->getTooltip())->toBeNull();
    });
});

describe('copying', function (): void {
    it('defaults `isCopyable()` to `false`', function (): void {
        $text = Text::make('Test');

        expect($text->isCopyable('Test'))->toBeFalse();
    });

    it('can set `copyable()`', function (): void {
        $text = Text::make('Test')->copyable();

        expect($text->isCopyable('Test'))->toBeTrue();
    });

    it('can set `copyable()` to `false`', function (): void {
        $text = Text::make('Test')->copyable()->copyable(false);

        expect($text->isCopyable('Test'))->toBeFalse();
    });

    it('can set `copyable()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->copyable(static fn (): bool => true);

        expect($text->isCopyable('Test'))->toBeTrue();
    });

    it('can set `copyableState()`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyableState('custom-state');

        expect($text->getCopyableState('Test'))->toBe('custom-state');
    });

    it('can set `copyableState()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyableState(static fn (): string => 'dynamic-state');

        expect($text->getCopyableState('Test'))->toBe('dynamic-state');
    });

    it('can set `copyMessage()`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyMessage('Copied!');

        expect($text->getCopyMessage('Test'))->toBe('Copied!');
    });

    it('can set `copyMessage()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyMessage(static fn (): string => 'Dynamic message');

        expect($text->getCopyMessage('Test'))->toBe('Dynamic message');
    });

    it('can set `copyMessageDuration()`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyMessageDuration(5000);

        expect($text->getCopyMessageDuration('Test'))->toBe(5000);
    });

    it('can set `copyMessageDuration()` with a `Closure`', function (): void {
        $text = Text::make('Test')
            ->copyable()
            ->copyMessageDuration(static fn (): int => 3000);

        expect($text->getCopyMessageDuration('Test'))->toBe(3000);
    });
});

describe('rendering', function (): void {
    it('can render with string content', function (): void {
        livewire(RenderText::class)->assertSuccessful()->assertSee('Hello world');
    });

    it('can render with `content()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureContent::class)->assertSuccessful()->assertSee('Dynamic');
    });

    it('can render with `badge()`', function (): void {
        livewire(RenderTextWithBadge::class)->assertSuccessful();
    });

    it('can render with `badge()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureBadge::class)->assertSuccessful();
    });

    it('can render with `size()` enum', function (): void {
        livewire(RenderTextWithSize::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `color()`', function (): void {
        livewire(RenderTextWithColor::class)->assertSuccessful();
    });

    it('can render with `color()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureColor::class)->assertSuccessful();
    });

    it('can render with `fontFamily()`', function (): void {
        livewire(RenderTextWithFontFamily::class)->assertSuccessful();
    });

    it('can render with `weight()`', function (): void {
        livewire(RenderTextWithWeight::class)->assertSuccessful();
    });

    it('can render with `icon()`', function (): void {
        livewire(RenderTextWithIcon::class)->assertSuccessful();
    });

    it('can render with `icon()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureIcon::class)->assertSuccessful();
    });

    it('can render with `tooltip()`', function (): void {
        livewire(RenderTextWithTooltip::class)->assertSuccessful();
    });

    it('can render with `tooltip()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureTooltip::class)->assertSuccessful();
    });

    it('can render with `copyable()`', function (): void {
        livewire(RenderTextWithCopyable::class)->assertSuccessful();
    });

    it('can render with `copyable()` set via `Closure`', function (): void {
        livewire(RenderTextWithClosureCopyable::class)->assertSuccessful();
    });
});

class RenderText extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Hello world')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureContent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->badge()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->badge(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->size(TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->size(static fn (): TextSize => TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithColor extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->color('success')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureColor extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->color(static fn (): string => 'danger')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithFontFamily extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->fontFamily(FontFamily::Mono)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithWeight extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->weight(FontWeight::Bold)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->icon(Heroicon::Check)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->icon(static fn () => Heroicon::Star)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithTooltip extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->tooltip('Tip')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureTooltip extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->tooltip(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithCopyable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->copyable()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextWithClosureCopyable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Text::make('Test')->copyable(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
