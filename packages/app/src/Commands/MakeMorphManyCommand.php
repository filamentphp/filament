<?php

namespace Filament\Commands;

class MakeMorphManyCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-morph-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';

    public function option($key = null)
    {
        if ($key === 'associate') {
            return true;
        }

        return parent::option($key);
    }
}
