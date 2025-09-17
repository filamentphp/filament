<?php

namespace Filament\Actions\Exports\Jobs;

use Closure;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
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

        $writeRowsFromFile = function (string $file, ?Style $style, ?Closure $makeRow) use ($csvDelimiter, $disk, $writer): void {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = (new Statement)->process($csvReader);

            foreach ($csvResults->getRecords() as $values) {
                $writer->addRow($makeRow($values, $style));
            }
        };

        $cellStyle = $this->exporter->getXlsxCellStyle();

        $writeRowsFromFile(
            $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv',
            $this->exporter->getXlsxHeaderCellStyle() ?? $cellStyle,
            $this->exporter->makeXlsxHeaderRow(...),
        );

        $makeRow = $this->exporter->makeXlsxRow(...);

        foreach ($disk->files($this->export->getFileDirectory()) as $file) {
            if (str($file)->endsWith('headers.csv')) {
                continue;
            }

            if (! str($file)->endsWith('.csv')) {
                continue;
            }

            $writeRowsFromFile($file, $cellStyle, $makeRow);
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
