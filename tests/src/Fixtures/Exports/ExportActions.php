<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ExportActions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public static bool $authorized = true;

    public static array $formats = [ExportFormat::Csv];

    public static ?int $maxRows = null;

    public static ?int $queryLimit = null;

    public static ?string $guard = null;

    public function exportAction(): ExportAction
    {
        return ExportAction::make()
            ->exporter(ActionPostExporter::class)
            ->authorize(static fn (): bool => static::$authorized)
            ->options(['minimum' => 7, 'static' => 'retained'])
            ->modifyQueryUsing(static fn (Builder $query, array $options): Builder => $query
                ->where('rating', '>=', $options['minimum'])
                ->when(static::$queryLimit !== null, static fn (Builder $query): Builder => $query->limit(static::$queryLimit)))
            ->fileDisk('local')
            ->fileName('selected-posts')
            ->maxRows(static::$maxRows)
            ->authGuard(static::$guard)
            ->formats(static::$formats);
    }

    public function render(): string
    {
        return '<div><x-filament-actions::modals /></div>';
    }
}
