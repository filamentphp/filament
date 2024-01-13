<?php

namespace Filament\Actions\Exports\Downloaders\Contracts;

use Filament\Actions\Exports\Models\Export;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface Downloader
{
    public function __invoke(Export $export): StreamedResponse;
}
