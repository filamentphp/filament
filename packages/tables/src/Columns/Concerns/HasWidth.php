<?php

namespace Filament\Tables\Columns\Concerns;

trait HasWidth
{
    public function columnWidth(int | string $width): static
    {
        if (is_int($width)) {
            $width = "{$width}px";
        }

        $this->extraHeaderAttributes[] = ['style' => 'width: ' . $width];

        return $this;
    }
}
