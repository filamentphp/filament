<?php

namespace Filament\Support\Actions\Contracts;

use Illuminate\Database\Eloquent\Model;

interface HasRecord
{
    public function getRecord(): ?Model;

    public function getRecordTitle(): ?string;
}
