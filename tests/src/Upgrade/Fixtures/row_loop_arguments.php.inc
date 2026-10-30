<?php

namespace Filament\Tests\Upgrade\Fixtures\RowLoopArguments;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\HasRowLoop;
use Filament\Tables\Columns\TextColumn;
use stdClass;

function configure(Column $column, ?Column $nullableColumn, stdClass $loop): array
{
    return [
        $column->rowLoop(loop: $loop)->getRowLoop(),
        $nullableColumn?->rowLoop(loop: $loop)?->getRowLoop(),
        TextColumn::make('computed')->rowLoop(loop: $loop)->getRowLoop(),
        ReusedRowLoop::make('reused')->rowLoop(loop: $loop)->getRowLoop(),
        $column->rowLoop(null)->getRowLoop(),
    ];
}

class ReusedRowLoop extends TextColumn
{
    use HasRowLoop;
}

class CustomRowLoop extends TextColumn
{
    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

class Unrelated
{
    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

function custom(CustomRowLoop $column, Unrelated $unrelated, stdClass $loop): void
{
    $column->rowLoop(loop: $loop);
    $unrelated->rowLoop(loop: $loop);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\RowLoopArguments;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\HasRowLoop;
use Filament\Tables\Columns\TextColumn;
use stdClass;

function configure(Column $column, ?Column $nullableColumn, stdClass $loop): array
{
    return [
        $column->rowLoop(rowLoop: $loop)->getRowLoop(),
        $nullableColumn?->rowLoop(rowLoop: $loop)?->getRowLoop(),
        TextColumn::make('computed')->rowLoop(rowLoop: $loop)->getRowLoop(),
        ReusedRowLoop::make('reused')->rowLoop(rowLoop: $loop)->getRowLoop(),
        $column->rowLoop(null)->getRowLoop(),
    ];
}

class ReusedRowLoop extends TextColumn
{
    use HasRowLoop;
}

class CustomRowLoop extends TextColumn
{
    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

class Unrelated
{
    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

function custom(CustomRowLoop $column, Unrelated $unrelated, stdClass $loop): void
{
    $column->rowLoop(loop: $loop);
    $unrelated->rowLoop(loop: $loop);
}
