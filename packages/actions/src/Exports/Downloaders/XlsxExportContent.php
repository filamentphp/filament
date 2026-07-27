<?php

namespace Filament\Actions\Exports\Downloaders;

use Filament\Actions\Exports\Models\Export;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class XlsxExportContent
{
    public function __invoke(Export $export, Writer $writer): void
    {
        $disk = $export->getFileDisk();
        $directory = $export->getFileDirectory();
        $csvDelimiter = $export->exporter::getCsvDelimiter();

        $writeRowsFromFile = function (string $file) use ($csvDelimiter, $disk, $writer): void {
            $csvReader = CsvReader::from($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = (new Statement)->process($csvReader);

            foreach ($csvResults->getRecords() as $row) {
                $writer->addRow(Row::fromValues($row));
            }
        };

        $writeRowsFromFile($directory . DIRECTORY_SEPARATOR . 'headers.csv');

        foreach ($disk->files($directory) as $file) {
            if (str($file)->endsWith('headers.csv')) {
                continue;
            }

            if (! str($file)->endsWith('.csv')) {
                continue;
            }

            $writeRowsFromFile($file);
        }
    }
}
