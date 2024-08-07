<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use InvalidArgumentException;

class Slider extends Field
{
//    use Concerns\CanBeAccepted;
//    use Concerns\CanBeInline;
//    use Concerns\CanFixIndistinctState;
//    use Concerns\HasToggleColors;
//    use Concerns\HasToggleIcons;
    use HasExtraAlpineAttributes;
    use Concerns\CanBeNative;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.slider';

    protected array | Closure | null $range = null;

    protected int | Closure |null $step = null;

    protected array | Closure | null $start = null;
    protected int | Closure | null $margin = null;

    protected int | Closure | null $limit = null;

    protected bool | Closure | null $connect = null;

    protected string | Closure | null $direction = null;

    protected string | Closure | null $orientation = null;

    protected string | Closure | null $behaviour = null;

    protected bool | Closure | null $tooltips = null;
    // To be added:
    // Put '0' at the bottom of the slider
    //
    //    format: wNumb({
    //      decimals: 0
    //    }),
    //
    //    // Show a scale with the   slider
    //    pips: {
    //        mode: 'steps',
    //            stepped: true,
    //            density: 4
    //        }
    protected function setUp(): void
    {
        parent::setUp();

//        $this->range(['min' => 0, 'max' => 100]);
    }

    public function range(array | Closure | null $range = null): static
    {
        if ( is_array($range) && (!array_key_exists('min', $range) || !array_key_exists('max', $range) || count($range) !== 2)) {
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

    public function start(array | Closure | null $start = null): static
    {
        if (empty($start)){
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

    public function connect(bool | Closure | null $connect = true): static
    {
        $this->connect = $connect;

        return $this;
    }

    public function direction(string | Closure | null $direction = 'ltr'): static
    {
        // These are the only accepted values.
        if (!in_array($direction, ['rtl', 'ltr'])) {
            throw new InvalidArgumentException("The direction must be 'rtl' or 'ltr'.");
        }

        $this->direction = $direction;

        return $this;
    }

    public function orientation(string | Closure | null $orientation = 'horizontal'): static
    {
        // These are the only accepted values.
        if (!in_array($orientation, ['vertical', 'horizontal'])) {
            throw new InvalidArgumentException("The orientation must be 'vertical' or 'horizontal'.");
        }

        $this->orientation = $orientation;

        return $this;
    }

    public function behaviour(array | Closure | null $behaviour = null): static
    {
        $acceptedValues = ['drag', 'drag-all', 'tap', 'fixed', 'snap', 'unconstrained', 'invert-connects', 'none'];

        if (is_array($behaviour)) {
            foreach ($behaviour as $value) {
                if (!in_array($value, $acceptedValues)) {
                    throw new InvalidArgumentException($value . ' is not an accepted value for the behaviour.');
                }
            }
            $behaviour = implode('-', $behaviour);
        }

        $this->behaviour = $behaviour;

        return $this;
    }

    public function tooltips(bool | Closure | null $tooltips = false): static
    {
        $this->tooltips = $tooltips;

        return $this;
    }

    public function getRange(): ?array
    {
        return $this->evaluate($this->range);
    }

    public function getStep(): ?int
    {
        return $this->evaluate($this->step);
    }

    public function getStart(): ?array
    {
        return $this->evaluate($this->start);
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

    public function getTooltips(): ?bool
    {
        return $this->evaluate($this->tooltips);
    }
}

