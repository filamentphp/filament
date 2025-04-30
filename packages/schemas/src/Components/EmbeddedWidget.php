<?php

namespace Filament\Schemas\Components;

use Closure;
use Exception;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class EmbeddedWidget extends Component
{
    /**
     * @param  array<string, mixed> | Closure  $livewireComponentData
     */
    public static function make(string | Closure $livewireComponent, array | Closure $livewireComponentData = []): Livewire
    {
        return Livewire::make($livewireComponent, $livewireComponentData);
    }

    public function render(): View
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof Widget)) {
            throw new Exception('The [' . $livewire::class . '] component must be a widget.');
        }

        return $livewire->render();
    }
}
