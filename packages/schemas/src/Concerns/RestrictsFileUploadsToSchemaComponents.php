<?php

namespace Filament\Schemas\Concerns;

trait RestrictsFileUploadsToSchemaComponents
{
    public function shouldRestrictFileUploadsToSchemaComponents(): bool
    {
        return true;
    }
}
