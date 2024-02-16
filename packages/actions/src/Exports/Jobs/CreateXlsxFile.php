<?php

namespace Filament\Actions\Exports\Jobs;

use Filament\Actions\Exports\CellStyle;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
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
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
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

        $writer = app(Writer::class);
        $writer->openToFile($temporaryFile = tempnam(sys_get_temp_dir(), $this->export->file_name));

        $headersStyle = $this->exporter->getHeadingStyle()
            ? $this->setStyle($this->exporter->getHeadingStyle())
            : null;

        $csvDelimiter = $this->exporter::getCsvDelimiter();

        $writeRowsFromFile = function (string $file, Style $headersStyle = null) use ($csvDelimiter, $disk, $writer) {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = Statement::create()->process($csvReader);

            foreach ($csvResults->getRecords() as $row) {
                $headersStyle
                    ? $writer->addRow(Row::fromValues($row, $headersStyle))
                    : $writer->addRow(Row::fromValues($row));
            }
        };

        $writeRowsFromFile($this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv', $headersStyle);

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

    /**
     * @param  CellStyle $cellStyle
     * @return Style
     */
    protected function setStyle(CellStyle $cellStyle): Style
    {
        $headingStyle = new Style();

        $styles = collect(get_object_vars($cellStyle))
            ->reject(fn (mixed $style): bool => $style === null || $style === false);

        foreach ($styles as $style => $value) {
            match ($style) {
                'bold'            => $headingStyle->setFontBold(),
                'italic'          => $headingStyle->setFontItalic(),
                'underline'       => $headingStyle->setFontUnderline(),
                'strikethrough'   => $headingStyle->setFontStrikethrough(),
                'size'            => $headingStyle->setFontSize($value),
                'family'          => $headingStyle->setFontName($value),
                'color'           => $headingStyle->setFontColor(
                    Color::rgb($value->red(), $value->green(), $value->blue())
                ),
                'backgroundColor' => $headingStyle->setBackgroundColor(
                    Color::rgb($value->red(), $value->green(), $value->blue())
                ),
                'verticalAlignment' => match ($styles->get('verticalAlignment')) {
                    VerticalAlignment::Start  => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::TOP),
                    VerticalAlignment::Center => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::CENTER),
                    VerticalAlignment::End    => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM),
                    default                   => null,
                },
                'alignment' => match ($styles->get('alignment')) {
                    Alignment::Start  => $headingStyle->setCellAlignment(CellAlignment::LEFT),
                    Alignment::Center => $headingStyle->setCellAlignment(CellAlignment::CENTER),
                    Alignment::End    => $headingStyle->setCellAlignment(CellAlignment::RIGHT),
                    default           => null,
                },
                default => null,
            };
        }

        return $headingStyle;
    }
}
