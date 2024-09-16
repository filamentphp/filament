<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Form;

trait CanBeMounted
{
    protected ?Closure $mountUsing = null;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function mount(array $parameters): mixed
    {
        return $this->evaluate($this->getMountUsing(), $parameters);
    }

    public function mountUsing(?Closure $callback): static
    {
        $this->mountUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function fillForm(array | Closure $data): static
    {
        $this->mountUsing(function (?Form $form) use ($data) {
            $form?->fill($this->evaluate($data));
        });

        return $this;
    }

    public function getMountUsing(): Closure
    {
        return $this->mountUsing ?? static function (?ComponentContainer $form = null): void {
            if (! $form) {
                return;
            }

            $form->fill();
        };
    }
}
