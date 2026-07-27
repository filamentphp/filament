<?php

namespace Filament\Actions\Exports\Downloaders;

use Filament\Actions\Exports\Models\Export;
use Generator;

class CsvExportContent
{
    /**
     * @return Generator<string>
     */
    public function __invoke(Export $export): Generator
    {
        $disk = $export->getFileDisk();
        $directory = $export->getFileDirectory();

        yield $disk->get($directory . DIRECTORY_SEPARATOR . 'headers.csv');

        foreach ($disk->files($directory) as $file) {
            if (str($file)->endsWith('headers.csv')) {
                continue;
            }

            if (! str($file)->endsWith('.csv')) {
                continue;
            }

            yield $disk->get($file);
        }
    }
}
