<?php

namespace Filament\Actions\Exports\Jobs;

use Filament\Actions\Exports\Enums\CellFont;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Support\Colors\Color;
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
use OpenSpout\Common\Entity\Style\Color as CellColor;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Spatie\Color\Rgb;

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

        $headersStyle = $this->setHeadersStyle(
            $this->exporter->getHeadersAlignment(),
            $this->exporter->getHeadersFontStyle()
        );

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
     * ToDo: Font size, bg color, border, docs
     *
     * @param array $alignment
     * @param array $fontStyle
     * @param array $colors
     * @return Style
     */
    private function setHeadersStyle(array $alignment, array $fontStyle): Style
    {
        $headingStyle = new Style();

        $styles = array_merge($alignment, $fontStyle);

        foreach ($styles as $style) {
            match ($style) {
                CellFont::Bold            => $headingStyle->setFontBold(),
                CellFont::Italic          => $headingStyle->setFontItalic(),
                CellFont::Underline       => $headingStyle->setFontUnderline(),
                CellFont::Strikethrough   => $headingStyle->setFontStrikethrough(),
                VerticalAlignment::Start  => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::TOP),
                VerticalAlignment::Center => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::CENTER),
                VerticalAlignment::End    => $headingStyle->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM),
                Alignment::Start          => $headingStyle->setCellAlignment(CellAlignment::LEFT),
                Alignment::Center         => $headingStyle->setCellAlignment(CellAlignment::CENTER),
                Alignment::End            => $headingStyle->setCellAlignment(CellAlignment::RIGHT),
                default                   => null,
            };

            if ($style instanceof \Spatie\Color\Rgb) {
                $headingStyle->setFontColor($style->red(), $style->green(), $style->blue());
            }
        }

        return $headingStyle;
    }
}
