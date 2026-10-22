<?php

namespace Filament\Schemas\Concerns;

use Filament\Support\Attributes\ServerOnly;

trait RestrictsFileUploadsToSchemaComponents
{
    #[ServerOnly]
    public function shouldRestrictFileUploadsToSchemaComponents(): bool
    {
        return true;
    }
}
