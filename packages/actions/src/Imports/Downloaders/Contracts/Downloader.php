<?php

namespace Filament\Actions\Imports\Downloaders\Contracts;

use Filament\Actions\Imports\Models\Import;
use Symfony\Component\HttpFoundation\Response;

interface Downloader
{
    public function __invoke(Import $import): Response;
}
