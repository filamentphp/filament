<?php

namespace Filament\Schema\Components\Decorations;

use Closure;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Concerns\HasWeight;
use Illuminate\Contracts\Support\Htmlable;

class TextDecoration extends Decoration
{
    use HasColor;
    use HasFontFamily;
    use HasTooltip;
    use HasWeight;

    protected string | Htmlable | Closure $content;

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

    public function getContent(): string | Htmlable
    {
        return $this->evaluate($this->content);
    }
}
