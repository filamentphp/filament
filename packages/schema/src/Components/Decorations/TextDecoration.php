<?php

namespace Filament\Schema\Components\Decorations;

use Closure;
use Filament\Schema\Components\Component;
use Filament\Schema\JsContent;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Concerns\HasWeight;
use Illuminate\Contracts\Support\Htmlable;

class TextDecoration extends Component
{
    use HasColor;
    use HasFontFamily;
    use HasIcon;
    use HasTooltip;
    use HasWeight;

    protected string | Htmlable | Closure $content;

    protected bool | Closure $isBadge = false;

    protected string $view = 'filament-schema::components.decorations.text-decoration';

    final public function __construct(string | Htmlable | Closure $content)
    {
        $this->content($content);
    }

    public static function make(string | Htmlable | Closure $content): static
    {
        $static = app(static::class, ['content' => $content]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor('gray');
    }

    public function content(string | Htmlable | Closure $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function js(): static
    {
        $this->content(JsContent::make($this->getContent()));

        return $this;
    }

    public function getContent(): string | Htmlable
    {
        return $this->evaluate($this->content);
    }
}
