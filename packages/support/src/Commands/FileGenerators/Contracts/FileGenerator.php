<?php

namespace Filament\Support\Commands\FileGenerators\Contracts;

interface FileGenerator
{
    public function generate(): string;
}
