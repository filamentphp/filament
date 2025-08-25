<?php

namespace Filament\Actions\Exports\Models;

use Carbon\CarbonInterface;
use Exception;
use Filament\Actions\Exports\Exporter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property CarbonInterface | null $completed_at
 * @property string $file_disk
 * @property string $file_name
 * @property class-string<Exporter> $exporter
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property-read Authenticatable $user
 */
class Export extends Model
{
    use Prunable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'timestamp',
            'processed_rows' => 'integer',
            'total_rows' => 'integer',
            'successful_rows' => 'integer',
        ];
    }

    protected $guarded = [];

    protected static bool $hasPolymorphicUserRelationship = false;

    public function user(): BelongsTo
    {
        if (static::hasPolymorphicUserRelationship()) {
            return $this->morphTo();
        }

        /** @var ?Authenticatable $authenticatable */
        $authenticatable = app(Authenticatable::class);

        if ($authenticatable) {
            /** @phpstan-ignore-next-line */
            return $this->belongsTo($authenticatable::class);
        }

        $userClass = app()->getNamespace() . 'Models\\User';

        if (! class_exists($userClass)) {
            throw new Exception('No [' . $userClass . '] model found. Please bind an authenticatable model to the [Illuminate\\Contracts\\Auth\\Authenticatable] interface in a service provider\'s [register()] method.');
        }

        /** @phpstan-ignore-next-line */
        return $this->belongsTo($userClass);
    }

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function getExporter(
        array $columnMap,
        array $options,
    ): Exporter {
        return app($this->exporter, [
            'export' => $this,
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

    public function getFileDisk(): Filesystem
    {
        return Storage::disk($this->file_disk);
    }

    public function getFileDirectory(): string
    {
        return 'filament_exports' . DIRECTORY_SEPARATOR . $this->getKey();
    }

    public function deleteFileDirectory(): void
    {
        $disk = $this->getFileDisk();
        $directory = $this->getFileDirectory();

        if ($disk->directoryExists($directory)) {
            $disk->deleteDirectory($directory);
        }
    }
}
