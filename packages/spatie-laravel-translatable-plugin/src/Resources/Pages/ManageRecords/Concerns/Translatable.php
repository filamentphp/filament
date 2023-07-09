<?php

namespace Filament\Resources\Pages\ManageRecords\Concerns;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable as ListRecordsTranslatable;
use Filament\SpatieLaravelTranslatableContentDriver;
use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait Translatable
{
    use ListRecordsTranslatable;
}
