<?php

namespace Filament\Actions\Exports\Jobs;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter($this->exporter::getCsvDelimiter());
        $csv->insertOne(array_values($this->columnMap));

        $filePath = $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv';
        $this->export->getFileDisk()->put($filePath, $csv->toString(), Filesystem::VISIBILITY_PRIVATE);

        $query = EloquentSerializeFacade::unserialize($this->query);
        $keyName = $query->getModel()->getKeyName();

        $exportCsvJob = $this->getExportCsvJob();

        $totalRows = 0;
        $page = 1;

        $dispatchRecords = function (array $records) use ($exportCsvJob, &$page, &$totalRows) {
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
                $jobs[] = new $exportCsvJob(
                    $this->export,
                    $this->query,
                    $recordsChunk,
                    $page,
                    $this->columnMap,
                    $this->options,
                );

                $page++;
            }

            $this->batch()->add($jobs);

            $totalRows += $recordsCount;
        };

        if ($this->records !== null) {
            $dispatchRecords($this->records);

            return;
        }

        $query->toBase()
            ->select([$query->getModel()->getQualifiedKeyName()])
            ->chunkById(
                $this->chunkSize * 10,
                fn (Collection $records) => $dispatchRecords(
                    Arr::pluck($records->all(), $keyName),
                ),
            );
    }

    public function getExportCsvJob(): string
    {
        return ExportCsv::class;
    }
}
