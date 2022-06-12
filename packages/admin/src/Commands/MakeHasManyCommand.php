<?php

namespace Filament\Commands;

class MakeHasManyCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-has-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}
