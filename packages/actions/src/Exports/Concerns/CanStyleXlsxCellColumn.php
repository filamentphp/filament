<?php

namespace Filament\Actions\Exports\Concerns;

use Closure;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;

trait CanStyleXlsxCellColumn
{
    /** @var array<Closure | Style> */
    protected array $xlsxCellColumnStylesUsing = [];

    protected Closure | Style | null $xlsxCellColumnStyleFromStateUsing = null;

    protected ?Style $cachedXlsxCellColumnStyle;

    public function xlsxCellColumnStyleUsing(Closure $callback): static
    {
        $this->xlsxCellColumnStylesUsing[] = $callback;

        return $this;
    }

    public function xlsxCellColumnStyleFromStateUsing(Closure | Style $xlsxCellColumnStyleFromStateUsing): static
    {
        $this->xlsxCellColumnStyleFromStateUsing = $xlsxCellColumnStyleFromStateUsing;

        return $this;
    }

    public function getXlsxCellColumnStyle(mixed $state = null): ?Style
    {
        $stateStyle = $this->evaluate($this->xlsxCellColumnStyleFromStateUsing, [
            'state' => $state,
        ]);

        if (empty($this->xlsxCellColumnStylesUsing)) {
            return $stateStyle;
        }

        $styleMerger = new StyleMerger;

        if (! isset($this->cachedXlsxCellColumnStyle)) {
            $cellColumnStyle = new Style;

            foreach ($this->xlsxCellColumnStylesUsing as $callbackOrStyle) {
                $style = $this->evaluate($callbackOrStyle);
                if ($style && $style instanceof Style) {
                    $cellColumnStyle = $styleMerger->merge(
                        $style,
                        $cellColumnStyle,
                    );
                }
            }

            $this->cachedXlsxCellColumnStyle = $cellColumnStyle;
        }

        if ($stateStyle === null) {
            return $this->cachedXlsxCellColumnStyle;
        }

        return $styleMerger->merge(
            $stateStyle,
            $this->cachedXlsxCellColumnStyle
        );
    }
}
