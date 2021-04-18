<?php

namespace Filament\Resources;

/**
 * This is a stub class for HasOne/BelongsTo relationships to add forms on a resource page.
 * It will work largely like the RelationManager, just without the table method.
 */
abstract class SubFormRelationManager extends RelationManager
{
    protected static $components = [
        'edit' => RelationManager\EditRecord::class,
    ];

    public function canCreate()
    {
        return false;
    }

    public function canAttach()
    {
        return false;
    }

    public function canDetach()
    {
        return false;
    }

    protected static function table(Table $table)
    {
        return $table;
    }
}
