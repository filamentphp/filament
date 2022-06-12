<?php

namespace Filament\Commands;

class MakeHasManyThroughCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-has-many-through {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}
