<?php

namespace Filament\Commands;

class MakeMorphManyCommand extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-morph-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}
