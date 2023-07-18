<?php

namespace Filament\Actions\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Model;

interface HasRecord
{
    public function record(Model | Closure | null $record): static;

    public function getRecord(): ?Model;

    public function getRecordTitle(): ?string;
}
