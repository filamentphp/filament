<?php

namespace Filament\Tables\Columns\Concerns;

trait HasWidth
{
    public function columnWidth(int|string $width): static
    {
        $this->extraHeaderAttributes[] = ['style' => 'width: ' . $width];

        return $this;
    }
}
