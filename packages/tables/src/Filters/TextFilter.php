<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TextFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->form([TextInput::make($name)])
            ->query(
                fn (Builder $query, array $data): Builder => $query->when(
                    $data[$name],
                    fn (Builder $query, $date): Builder => $query->where($name, $data[$name]),
                )
            );
    }
}
