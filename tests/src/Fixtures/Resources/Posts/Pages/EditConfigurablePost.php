<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;

class EditConfigurablePost extends EditRecord
{
    protected static string $resource = ConfigurablePostResource::class;
}
