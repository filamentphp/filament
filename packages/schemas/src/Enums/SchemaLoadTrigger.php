<?php

namespace Filament\Schemas\Enums;

enum SchemaLoadTrigger
{
    case InViewport;

    case AfterRender;
}
