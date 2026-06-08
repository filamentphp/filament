<?php

namespace Filament\Schemas\Concerns;

trait RestrictsFileUploadsToSchemaComponents
{
    protected function shouldRestrictFileUploadsToSchemaComponents(): bool
    {
        return true;
    }
}
