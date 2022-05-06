<?php

namespace Filament\Forms\Components\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface CanEntangleWithSingularRelationships
{
    public function getRelationship(): ?HasOne;

    public function getRelatedRecord(): ?Model;
}
