<?php

namespace Filament\Actions\Exports\Jobs;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Csv\Bom;
use League\Csv\Writer;
use SplTempFileObject;

class PrepareCsvExport implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Exporter $exporter;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     * @param  array<mixed> | null  $records
     */
    public function __construct(
        protected Export $export,
        protected string $query,
        protected array $columnMap,
        protected array $options = [],
        protected int $chunkSize = 100,
        protected ?array $records = null,
    ) {
        $this->exporter = $this->export->getExporter(
            $this->columnMap,
            $this->options,
        );
    }

    public function handle(): void
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->setOutputBOM(Bom::Utf8);
        $csv->setDelimiter($this->exporter::getCsvDelimiter());
        $csv->insertOne(array_values($this->columnMap));

        $filePath = $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv';
        $this->export->getFileDisk()->put($filePath, $csv->toString(), Filesystem::VISIBILITY_PRIVATE);

        $query = EloquentSerializeFacade::unserialize($this->query);
        $keyName = $query->getModel()->getKeyName();
        $qualifiedKeyName = $query->getModel()->getQualifiedKeyName();

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        if ($databaseConnection->getDriverName() === 'pgsql') {
            $originalOrders = collect($query->getQuery()->orders)
                ->reject(function (array $order) use ($qualifiedKeyName): bool {
                    if (($order['type'] ?? null) === 'Raw') {
                        return false;
                    }

                    return ($order['column'] ?? null) === $qualifiedKeyName;
                })
                ->unique(function (array $order): string {
                    if (($order['type'] ?? null) === 'Raw') {
                        return 'raw:' . ($order['sql'] ?? '');
                    }

                    return 'column:' . ($order['column'] ?? '');
                });

            /** @var array<string, mixed> $originalBindings */
            $originalBindings = $query->getRawBindings();

            if (! empty($originalOrders->all())) {
                $firstOrder = $originalOrders->first();

                if (($firstOrder['type'] ?? null) === 'Raw') {
                    $query->reorder();
                    $query->orderByRaw($firstOrder['sql']);
                } else {
                    $query->reorder($firstOrder['column'], $firstOrder['direction']);
                }

                $originalOrders->forget(0);
            } else {
                $query->reorder($qualifiedKeyName);
            }

            foreach ($originalOrders as $order) {
                if (($order['type'] ?? null) === 'Raw') {
                    $query->orderByRaw($order['sql']);
                } elseif (filled($order['column'] ?? null) && filled($order['direction'] ?? null)) {
                    $query->orderBy($order['column'], $order['direction']);
                }
            }

            $newBindings = $query->getRawBindings();

            foreach ($originalBindings as $key => $value) {
                if ($binding = array_diff($value, $newBindings[$key])) {
                    $query->addBinding($binding, $key);
                }
            }
        }

        $exportCsvJob = $this->getExportCsvJob();

        $totalRows = 0;
        $page = 1;

        // We do not want to send the loaded user relationship to the queue in job payloads,
        // in case it contains attributes that are not serializable, such as binary columns.
        $this->export->unsetRelation('user');

        $dispatchRecords = function (array $records) use ($exportCsvJob, &$page, &$totalRows): void {
            $recordsCount = count($records);

            if (($totalRows + $recordsCount) > $this->export->total_rows) {
                $records = array_slice($records, 0, $this->export->total_rows - $totalRows);
                $recordsCount = count($records);
            }

            if (! $recordsCount) {
                return;
            }

            $jobs = [];

            foreach (array_chunk($records, length: $this->chunkSize) as $recordsChunk) {
                $jobs[] = app($exportCsvJob, [
                    'export' => $this->export,
                    'query' => $this->query,
                    'records' => $recordsChunk,
                    'page' => $page,
                    'columnMap' => $this->columnMap,
                    'options' => $this->options,
                ]);

                $page++;
            }

            $this->batch()->add($jobs);

            $totalRows += $recordsCount;
        };

        if ($this->records !== null) {
            $dispatchRecords($this->records);

            return;
        }

        $chunkKeySize = $this->chunkSize * 10;

        $baseQuery = $query->toBase();

        if (in_array($query->getQuery()->orders[0]['column'] ?? null, [$keyName, $qualifiedKeyName])) {
            $baseQuery->distinct($qualifiedKeyName);
        }

        /** @phpstan-ignore-next-line */
        $baseQueryOrders = $baseQuery->orders ?? [];
        $baseQueryOrdersCount = count($baseQueryOrders);

        if (
            (
                ($baseQueryOrdersCount === 1) &&
                (! in_array($baseQueryOrders[0]['column'] ?? null, [$keyName, $qualifiedKeyName]))
            ) ||
            ($baseQueryOrdersCount > 1)
        ) {
            $baseQuery->chunk(
                $this->chunkSize,
                fn (Collection $records) => $dispatchRecords(
                    Arr::pluck($records->all(), $keyName),
                ),
            );

            return;
        }

        $baseQuery
            ->select([$qualifiedKeyName])
            ->orderedChunkById(
                $chunkKeySize,
                fn (Collection $records) => $dispatchRecords(
                    Arr::pluck($records->all(), $keyName),
                ),
                column: $qualifiedKeyName,
                alias: $keyName,
                descending: ($baseQueryOrders[0]['direction'] ?? 'asc') === 'desc',
            );
    }

    public function getExportCsvJob(): string
    {
        return ExportCsv::class;
    }
}
