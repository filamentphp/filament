<?php

namespace Filament\Commands;

class MakeMorphToManyCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-morph-to-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}
