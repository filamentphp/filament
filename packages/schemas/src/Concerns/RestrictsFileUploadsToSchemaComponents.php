<?php

namespace Filament\Schemas\Concerns;

use Filament\Support\Components\Attributes\Unexposed;

trait RestrictsFileUploadsToSchemaComponents
{
    #[Unexposed]
    public function shouldRestrictFileUploadsToSchemaComponents(): bool
    {
        return true;
    }
}
