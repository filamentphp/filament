<?php

namespace Filament\Actions\Imports\Models;

use Carbon\CarbonInterface;
use Exception;
use Filament\Actions\Imports\Importer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CarbonInterface | null $completed_at
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
    use Prunable;

    protected $casts = [
        'completed_at' => 'timestamp',
        'processed_rows' => 'integer',
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
    ];

    protected $guarded = [];

    protected static bool $hasPolymorphicUserRelationship = false;

    public function failedRows(): HasMany
    {
        return $this->hasMany(app(FailedImportRow::class)::class);
    }

    public function user(): BelongsTo
    {
        if (static::hasPolymorphicUserRelationship()) {
            return $this->morphTo();
        }

        $authenticatable = app(Authenticatable::class);

        if ($authenticatable) {
            return $this->belongsTo($authenticatable::class);
        }

        if (! class_exists(\App\Models\User::class)) {
            throw new Exception('No [App\\Models\\User] model found. Please bind an authenticatable model to the [Illuminate\\Contracts\\Auth\\Authenticatable] interface in a service provider\'s [register()] method.');
        }

        return $this->belongsTo(\App\Models\User::class);
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

    public static function polymorphicUserRelationship(bool $condition = true): void
    {
        static::$hasPolymorphicUserRelationship = $condition;
    }

    public static function hasPolymorphicUserRelationship(): bool
    {
        return static::$hasPolymorphicUserRelationship;
    }
}
