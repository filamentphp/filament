<?php

namespace Filament\Actions\Exports\Jobs;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;
use Throwable;

class ExportCsv implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Exporter $exporter;

    public ?int $maxExceptions = 5;

    /**
     * @param  array<mixed>  $records
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Export $export,
        protected string $query,
        protected array $records,
        protected int $page,
        protected array $columnMap,
        protected array $options = [],
    ) {
        $this->exporter = $this->export->getExporter(
            $this->columnMap,
            $this->options,
        );
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return $this->exporter->getJobMiddleware();
    }

    public function handle(): void
    {
        /** @var Authenticatable $user */
        $user = $this->export->user;

        auth()->setUser($user);

        $processedRows = 0;
        $successfulRows = 0;

        $csv = Writer::from(new SplTempFileObject);
        $csv->setDelimiter($this->exporter::getCsvDelimiter());

        $query = EloquentSerializeFacade::unserialize($this->query);

        foreach ($this->exporter->getCachedColumns() as $column) {
            $column->applyRelationshipAggregates($query);
            $column->applyEagerLoading($query);
        }

        foreach ($query->find($this->records) as $record) {
            try {
                $csv->insertOne(($this->exporter)($record));

                $successfulRows++;
            } catch (Throwable $exception) {
                report($exception);
            }

            $processedRows++;
        }

        $filePath = $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . str_pad(strval($this->page), 16, '0', STR_PAD_LEFT) . '.csv';

        DB::transaction(function () use ($csv, $filePath, $processedRows, $successfulRows): void {
            $this->export::query()
                ->whereKey($this->export->getKey())
                ->lockForUpdate()
                ->update([
                    'processed_rows' => new Expression('processed_rows + ' . $processedRows),
                    'successful_rows' => new Expression('successful_rows + ' . $successfulRows),
                ]);

            $this->export::query()
                ->whereKey($this->export->getKey())
                ->whereColumn('processed_rows', '>', 'total_rows')
                ->lockForUpdate()
                ->update([
                    'processed_rows' => new Expression('total_rows'),
                ]);

            $this->export::query()
                ->whereKey($this->export->getKey())
                ->whereColumn('successful_rows', '>', 'total_rows')
                ->lockForUpdate()
                ->update([
                    'successful_rows' => new Expression('total_rows'),
                ]);

            $this->export->getFileDisk()->put($filePath, $csv->toString(), Filesystem::VISIBILITY_PRIVATE);
        });
    }

    public function retryUntil(): ?CarbonInterface
    {
        return $this->exporter->getJobRetryUntil();
    }

    /**
     * @return int | array<int> | null
     */
    public function backoff(): int | array | null
    {
        return $this->exporter->getJobBackoff();
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->exporter->getJobTags();
    }
}
