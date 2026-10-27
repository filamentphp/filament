<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksCrossFile;

class ChildStats extends ParentStats
{
    protected function getStats(): array
    {
        return [...parent::getStats()];
    }
}

class ModernAlignmentChild extends ParentAlignment
{
    public static function alignFormActionsStart(): void {}
}

class LegacyAlignmentChild extends ParentAlignment
{
    public static function alignFormActionsLeft(): void {}
}

function callback(): array
{
    return [ExternallyCalledLabel::class, 'getLabel'];
}
