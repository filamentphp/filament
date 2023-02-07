<?php

namespace Filament\Actions;

use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class SelectAction extends Action
{
    use Concerns\HasId;

    /**
     * @var array<string> | Arrayable | string | Closure
     */
    protected array | Arrayable | string | Closure $options = [];

    protected ?string $placeholder = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-actions::select-action');
    }

    /**
     * @param  array<string> | Arrayable | Closure  $options
     */
    public function options(array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        $enum = $options;
        if (
            is_string($enum) &&
            function_exists('enum_exists') &&
            enum_exists($enum)
        ) {
            return collect($enum::cases())
                ->when(
                    is_a($enum, LabelInterface::class, allow_string: true),
                    fn (Collection $options): Collection => $options
                        ->mapWithKeys(fn ($case) => [
                            ($case?->value ?? $case->name) => $case->getLabel() ?? $case->name,
                        ]),
                    fn (Collection $options): Collection => $options
                        ->mapWithKeys(fn ($case) => [
                            ($case?->value ?? $case->name) => $case->name,
                        ]),
                )
                ->all();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }
}
