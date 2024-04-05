<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Concerns\HasIconColor;

trait HasHeader
{
    use HasIconColor;

    protected string | Closure | null $icon = null;

    protected IconSize | string | Closure | null $iconSize = null;
    
    protected string | Htmlable | Closure | null $heading = null;

    protected View | Htmlable | Closure | null $header = null;

    protected string | Htmlable | Closure | null $description = null;

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconSize(IconSize | string | Closure | null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function description(string | Htmlable | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function header(View | Htmlable | Closure | null $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function heading(string | Htmlable | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getIconSize(): IconSize | string | null
    {
        return $this->evaluate($this->iconSize);
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }
}
