<?php

namespace Filament\GlobalSearch\Contracts;

interface GlobalSearchProvider
{
    public function getResults(string $query): ?array;
}
