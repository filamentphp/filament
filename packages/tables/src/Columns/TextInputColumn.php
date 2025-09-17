<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Tables\Table;
use Illuminate\Support\Js;
use Filament\Support\RawJs;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Forms\Components\Concerns\HasInputMode;
use Filament\Tables\Columns\Concerns\HasExtraContent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;

class TextInputColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasExtraContent;
    use HasExtraInputAttributes;
    use HasInputMode;
    use HasStep;

    protected string | RawJs | Closure | null $mask = null;

    protected string | Closure | null $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type) ?? 'text';
    }

    public function mask(string | RawJs | Closure | null $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask(): string | RawJs | null
    {
        return $this->evaluate($this->mask);
    }

    public function toEmbeddedHtml(): string
    {
        $html = parent::toEmbeddedHtml();
        
        $html = $this->insertExtraContent($html);
        
        return $html;
    }

    protected function insertExtraContent(string $html): string
    {
        $extraContent = '';
        
        if ($this->hasExtraContent('above_content')) {
            $extraContent .= '<div class="fi-ta-text-input-above-content mb-1">' . $this->renderExtraContent('above_content') . '</div>';
        }
        
        if ($this->hasExtraContent('above_error')) {
            $extraContent .= '<div class="fi-ta-text-input-above-error text-xs text-gray-500 mb-1">' . $this->renderExtraContent('above_error') . '</div>';
        }
        
        if ($this->hasExtraContent('below_error')) {
            $extraContent .= '<div class="fi-ta-text-input-below-error text-xs text-gray-500 mt-1">' . $this->renderExtraContent('below_error') . '</div>';
        }
        
        if ($this->hasExtraContent('below_content')) {
            $extraContent .= '<div class="fi-ta-text-input-below-content mt-1">' . $this->renderExtraContent('below_content') . '</div>';
        }
        
        if ($extraContent) {
            $lastDivPos = strrpos($html, '</div>');
            if ($lastDivPos !== false) {
                $html = substr_replace($html, $extraContent, $lastDivPos, 0);
            }
        }
        
        return $html;
    }
}
