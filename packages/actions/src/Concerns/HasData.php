<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasData
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    protected ?Closure $mutateDataUsing = null;

    public function mutateDataUsing(?Closure $callback): static
    {
        $this->mutateDataUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `mutateDataUsing()` instead.
     */
    public function mutateFormDataUsing(?Closure $callback): static
    {
        $this->mutateDataUsing($callback);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function data(array $data, bool $shouldMutate = true): static
    {
        if ($shouldMutate && $this->mutateDataUsing) {
            $data = $this->evaluate($this->mutateDataUsing, [
                'data' => $data,
            ]);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * @deprecated Use `data()` instead.
     *
     * @param  array<string, mixed>  $data
     */
    public function formData(array $data, bool $shouldMutate = true): static
    {
        $this->data($data, $shouldMutate);

        return $this;
    }

    public function resetData(): static
    {
        $this->data([], shouldMutate: false);

        return $this;
    }

    /**
     * @deprecated Use `resetData()` instead.
     */
    public function resetFormData(): static
    {
        $this->resetData();

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @deprecated Use `getData()` instead.
     *
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return $this->getData();
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->getLivewire()->mountedActions[$this->getNestingIndex()]['data'] ?? [];
    }

    /**
     * @deprecated Use `getRawData()` instead.
     *
     * @return array<string, mixed>
     */
    public function getRawFormData(): array
    {
        return $this->getRawData();
    }
}
