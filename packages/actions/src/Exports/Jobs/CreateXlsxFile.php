<?php

namespace Filament\Actions\Exports\Jobs;

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
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class CreateXlsxFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        protected Export $export,
    ) {
    }

    public function handle(): void
    {
        $disk = $this->export->getFileDisk();

        $writer = app(Writer::class);
        $writer->openToFile($temporaryFile = tempnam(sys_get_temp_dir(), $this->export->file_name));

        $csvDelimiter = $this->export->exporter::getCsvDelimiter();

        $writeRowsFromFile = function (string $file) use ($csvDelimiter, $disk, $writer) {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = Statement::create()->process($csvReader);

            foreach ($csvResults->getRecords() as $row) {
                $writer->addRow(Row::fromValues($row));
            }
        };

        $writeRowsFromFile($this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv');

        foreach ($disk->files($this->export->getFileDirectory()) as $file) {
            if (str($file)->endsWith('headers.csv')) {
                continue;
            }

            if (! str($file)->endsWith('.csv')) {
                continue;
            }

            $writeRowsFromFile($file);
        }

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
