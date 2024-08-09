<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Enums\SliderBehaviour;
use Filament\Forms\Enums\SliderDirection;
use Filament\Forms\Enums\SliderOrientation;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\RawJs;
use InvalidArgumentException;

class Slider extends Field
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeValidated;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.slider';

    /** @var array<string, int>|Closure|null */
    protected array | Closure | null $range = null;

    protected int | Closure | null $step = null;

    /** @var array<int>|int|Closure|null */
    protected array | int | Closure | null $start = null;

    protected int | Closure | null $margin = null;

    protected int | Closure | null $limit = null;

    protected bool | Closure | null $connect = null;

    protected string | Closure | null $direction = null;

    protected string | Closure | null $orientation = null;

    /** @var string|Closure|null */
    protected string | Closure | null $behaviour = null;

    /** @var array<int, bool>|bool|Closure|null */
    protected array | bool | Closure | null $tooltips = null;

    protected RawJs | Closure | null $format = null;

    protected RawJs | Closure | null $ariaFormat = null;

    protected RawJs | Closure | null $pips = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @param array<string, int>|Closure $range */
    public function range(array | Closure $range): static
    {
        if (is_array($range) && (!array_key_exists('min', $range) || !array_key_exists('max', $range) || count($range) !== 2)) {
            throw new InvalidArgumentException("The range array must have 'min' and 'max' keys.");
        }

        $this->range = $range;

        return $this;
    }

    public function step(int | Closure | null $step = null): static
    {
        $this->step = $step;

        return $this;
    }

    /** @param array<int>|int|Closure $start */
    public function start(array | int | Closure $start): static
    {
        if (empty($start)) {
            $start = null;
        }

        $this->start = $start;

        return $this;
    }

    public function margin(int | Closure | null $margin = null): static
    {
        $this->margin = $margin;

        return $this;
    }

    public function limit(int | Closure | null $limit = null): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function connect(bool | Closure | null $connect = null): static
    {
        $this->connect = $connect;

        return $this;
    }

    public function direction(SliderDirection | Closure | null $direction = SliderDirection::LTR): static
    {
        // Convert the enum to its string value before assignment
        if ($direction instanceof SliderDirection) {
            $direction = $direction->value;
        }

        $this->direction = $direction;

        return $this;
    }

    public function orientation(SliderOrientation | Closure | null $orientation = SliderOrientation::Horizontal): static
    {
        // Convert the enum to its string value before assignment
        if ($orientation instanceof SliderOrientation) {
            $orientation = $orientation->value;
        }

        $this->orientation = $orientation;

        return $this;
    }

    /** @param array<SliderBehaviour>|SliderBehaviour|Closure|null $behaviour */
    public function behaviour(array | SliderBehaviour | Closure | null $behaviour = null): static
    {
        if (is_array($behaviour)) {
            $behaviourStrings = array_map(fn($item) => $item->value, $behaviour);
            $behaviour = implode('-', $behaviourStrings);
        } elseif ($behaviour instanceof SliderBehaviour) {
            $behaviour = $behaviour->value;
        }

        $this->behaviour = $behaviour;

        return $this;
    }

    /** @param array<int, bool>|bool|Closure|null $tooltips */
    public function tooltips(array | bool | Closure | null $tooltips = false): static
    {
        $this->tooltips = $tooltips;

        return $this;
    }

    public function format(RawJs | Closure | null $format = null): static
    {
        $this->format = $format;

        return $this;
    }

    public function pips(RawJs | Closure | null $pips = null): static
    {
        $this->pips = $pips;

        return $this;
    }

    public function ariaFormat(RawJs | Closure | null $ariaFormat = null): static
    {
        $this->ariaFormat = $ariaFormat;

        return $this;
    }

    /** @return array<string, int>|null */
    public function getRange(): ?array
    {
        return $this->evaluate($this->range ?? ['min' => 0, 'max' => 100]);
    }

    public function getStep(): ?int
    {
        return $this->evaluate($this->step);
    }

    /** @return int|array<int>|null */
    public function getStart(): int | array | null
    {
        return $this->evaluate($this->start ?? 0);
    }

    public function getMargin(): ?int
    {
        return $this->evaluate($this->margin);
    }

    public function getLimit(): ?int
    {
        return $this->evaluate($this->limit);
    }

    public function getConnect(): ?bool
    {
        return $this->evaluate($this->connect);
    }

    public function getDirection(): ?string
    {
        return $this->evaluate($this->direction);
    }

    public function getOrientation(): ?string
    {
        return $this->evaluate($this->orientation);
    }

    public function getBehaviour(): ?string
    {
        return $this->evaluate($this->behaviour);
    }

    /** @return bool|array<int, bool>|null */
    public function getTooltips(): bool | array | null
    {
        return $this->evaluate($this->tooltips);
    }

    public function getFormat(): ?RawJs
    {
        return $this->evaluate($this->format);
    }

    public function getPips(): ?RawJs
    {
        return $this->evaluate($this->pips);
    }

    public function getAriaFormat(): ?RawJs
    {
        return $this->evaluate($this->ariaFormat);
    }
}
