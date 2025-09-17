<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Exception;
use Illuminate\Contracts\Support\Htmlable;

trait HasExtraContent
{
    /**
     * @var array<string, string|Htmlable|Closure|null>
     */
    protected array $extraContent = [];

    public function aboveErrorMessage(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['above_error'] = $content;

        return $this;
    }

    public function belowErrorMessage(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['below_error'] = $content;

        return $this;
    }

    public function aboveContent(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['above_content'] = $content;

        return $this;
    }

    public function belowContent(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['below_content'] = $content;

        return $this;
    }

    public function aboveLabel(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['above_label'] = $content;

        return $this;
    }

    public function belowLabel(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['below_label'] = $content;

        return $this;
    }

    public function beforeLabel(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['before_label'] = $content;

        return $this;
    }

    public function afterLabel(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['after_label'] = $content;

        return $this;
    }

    public function beforeContent(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['before_content'] = $content;

        return $this;
    }

    public function afterContent(string | Htmlable | Closure | null $content): static
    {
        $this->extraContent['after_content'] = $content;

        return $this;
    }

    protected function renderExtraContent(string $key): string
    {
        $content = $this->extraContent[$key] ?? null;

        if ($content === null) {
            return '';
        }

        if (is_string($content)) {
            return e($content);
        }

        if ($content instanceof Htmlable) {
            return $content->toHtml();
        }

        try {
            $record = $this->getRecord();

            return $content($record);
        } catch (Exception $e) {
            return $content();
        }
    }

    protected function hasExtraContent(string $key): bool
    {
        return ! empty($this->extraContent[$key] ?? null);
    }
}
