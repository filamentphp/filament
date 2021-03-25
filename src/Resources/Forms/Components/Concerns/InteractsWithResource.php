<?php

namespace Filament\Resources\Forms\Components\Concerns;

trait InteractsWithResource
{
    use CanBeDependentOnResourceRecord;
    use CanServeResourceSubform;
    use ManipulatesResourceRecord;
}
