<?php

namespace Filament\Widgets;

use Closure;
use Filament\Tables;
use Illuminate\Support\Str;

class TableWidget extends Widget implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::widgets.table-widget';

    protected static ?string $heading = null;

    protected function getTableHeading(): string | Closure | null
    {
        return static::$heading ?? (string) Str::of(class_basename(static::class))
            ->beforeLast('Widget')
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }
}
