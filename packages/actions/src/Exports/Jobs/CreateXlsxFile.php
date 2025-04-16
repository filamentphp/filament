<?php

namespace Filament\Actions\Exports\Jobs;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class CreateXlsxFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Exporter $exporter;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Export $export,
        protected array $columnMap,
        protected array $options = [],
    ) {
        $this->exporter = $this->export->getExporter(
            $this->columnMap,
            $this->options,
        );
    }

    public function handle(): void
    {
        $disk = $this->export->getFileDisk();

        $writer = app(Writer::class, ['options' => $this->exporter->getXlsxWriterOptions()]);
        $writer->openToFile($temporaryFile = tempnam(sys_get_temp_dir(), $this->export->file_name));

        $csvDelimiter = $this->exporter::getCsvDelimiter();

        $exportColumnsByIndex = array_values($this->exporter->getCachedColumns());

        $writeRowsFromFile = function (string $file, bool $isHeader, ?Style $style = null) use ($csvDelimiter, $disk, $writer, $exportColumnsByIndex): void {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = (new Statement)->process($csvReader);

            foreach ($csvResults->getRecords() as $row) {
                if ($isHeader) {
                    $writer->addRow(Row::fromValues($row, $style));
                } else {
                    $columnsFormats = Arr::map(
                        $row,
                        fn (string $value, $index) => $exportColumnsByIndex[$index]->getXlsxCellColumnStyle($value)
                    );
                    $writer->addRow(Row::fromValuesWithStyles($row, $style, $columnsFormats));
                }
            }
        };

        $cellStyle = $this->exporter->getXlsxCellStyle();

        $writeRowsFromFile(
            $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv',
            true,
            $this->exporter->getXlsxHeaderCellStyle() ?? $cellStyle,
        );

        foreach ($disk->files($this->export->getFileDirectory()) as $file) {
            if (str($file)->endsWith('headers.csv')) {
                continue;
            }

            if (! str($file)->endsWith('.csv')) {
                continue;
            }

            $writeRowsFromFile($file, false, $cellStyle);
        }

        $this->exporter->configureXlsxWriterBeforeClose($writer);

        $writer->close();

        $disk->putFileAs(
            $this->export->getFileDirectory(),
            new File($temporaryFile),
            "{$this->export->file_name}.xlsx",
            Filesystem::VISIBILITY_PRIVATE,
        );

        unlink($temporaryFile);
    }
}
