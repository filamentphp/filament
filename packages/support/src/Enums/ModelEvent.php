<?php

namespace Filament\Support\Enums;

enum ModelEvent: string
{
    case Created = 'created';

    case Updated = 'updated';

    case Saved = 'saved';

    case Deleted = 'deleted';

    case Trashed = 'trashed';

    case Restored = 'restored';

    case ForceDeleted = 'forceDeleted';
}
