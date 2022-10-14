<?php

namespace Filament\Commands;

class MakeBelongsToManyCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-belongs-to-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';

    public function option($key = null)
    {
        if ($key === 'attach') {
            return true;
        }

        return parent::option($key);
    }
}
