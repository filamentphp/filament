<?php

namespace Filament\Schemas\Components;

use Closure;
use Exception;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;

class EmbeddedTable extends Component
{
    /**
     * @param  array<string, mixed> | Closure  $livewireComponentData
     */
    public static function make(string | Closure | null $livewireComponent = null, array | Closure $livewireComponentData = []): static | Livewire
    {
        if (filled($livewireComponent)) {
            return Livewire::make($livewireComponent, $livewireComponentData);
        }

        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function render(): View
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof HasTable)) {
            throw new Exception('The [' . $livewire::class . '] component must have a table defined.');
        }

        return $livewire->getTable()->render();
    }
}
