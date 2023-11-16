<?php

namespace Filament\Actions\Imports\Models;

use Carbon\CarbonInterface;
use Filament\Actions\Imports\Importer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CarbonInterface | null $completed_at
 * @property CarbonInterface | null $failed_at
 * @property string $file_name
 * @property string $file_path
 * @property class-string<Importer> $importer
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property-read Collection<FailedImportRow> $failedRows
 * @property-read Authenticatable $user
 */
class Import extends Model
{
    protected $casts = [
        'completed_at' => 'timestamp',
        'processed_rows' => 'integer',
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
    ];

    protected $guarded = [];

    public function failedRows(): HasMany
    {
        return $this->hasMany(app(FailedImportRow::class)::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(app(Authenticatable::class)::class);
    }

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function getImporter(
        array $columnMap,
        array $options,
    ): Importer {
        return app($this->importer, [
            'import' => $this,
            'columnMap' => $columnMap,
            'options' => $options,
        ]);
    }

    public function getFailedRowsCount(): int
    {
        return $this->total_rows - $this->successful_rows;
    }
}
