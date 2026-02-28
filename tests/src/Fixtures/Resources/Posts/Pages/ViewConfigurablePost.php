<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;

class ViewConfigurablePost extends ViewRecord
{
    protected static string $resource = ConfigurablePostResource::class;
}
