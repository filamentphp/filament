<?php

namespace Filament\Support\Concerns;

use Closure;
use League\CommonMark\Extension\ExtensionInterface;

trait CanConfigureCommonMark
{
    /**
     * @var array<string, mixed> | Closure | null
     */
    protected array | Closure | null $commonMarkOptions = null;

    /**
     * @var array<array-key, ExtensionInterface> | Closure | null
     */
    protected array | Closure | null $commonMarkExtensions = null;

    /**
     * @param  array<string, mixed> | Closure | null  $commonMarkOptions
     */
    public function commonMarkOptions(array | Closure | null $commonMarkOptions): static
    {
        $this->commonMarkOptions = $commonMarkOptions;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCommonMarkOptions(): array
    {
        return $this->evaluate($this->commonMarkOptions) ?? [];
    }

    /**
     * @param  array<array-key, ExtensionInterface> | Closure | null  $commonMarkExtensions
     */
    public function commonMarkExtensions(array | Closure | null $commonMarkExtensions): static
    {
        $this->commonMarkExtensions = $commonMarkExtensions;

        return $this;
    }

    /**
     * @return array<array-key, ExtensionInterface>
     */
    public function getCommonMarkExtensions(): array
    {
        return $this->evaluate($this->commonMarkExtensions) ?? [];
    }
}
