<?php

namespace Filament\Tests\Upgrade\Fixtures\ConfigurationHooksManual;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class BothNames extends Page
{
    protected function getActions(): array { return []; }
    protected function getHeaderActions(): array { return []; }
}

class LegacyPage extends Page
{
    protected function getActions(): array { return []; }
}

class ConflictingChildPage extends LegacyPage
{
    protected function getActions(): array { return parent::getActions(); }
    protected function getHeaderActions(): array { return []; }
}

class CanonicalPage extends Page
{
    protected function getHeaderActions(): array { return []; }
}

class InactiveLegacyPage extends CanonicalPage
{
    protected function getActions(): array { return []; }
}

class BothLabels extends Resource
{
    protected static ?string $label = 'old';
    protected static ?string $modelLabel = '';
    protected static ?string $pluralLabel = 'older';
    protected static ?string $pluralModelLabel = '';
}

class LegacyLabels extends Resource
{
    protected static ?string $label = 'Parent';
}

class CanonicalChildLabels extends LegacyLabels
{
    protected static ?string $modelLabel = 'Child';
}

class CanonicalLabels extends Resource
{
    protected static ?string $modelLabel = 'Parent';
}

class LegacyChildLabels extends CanonicalLabels
{
    protected static ?string $label = 'Child';
}

class ReadLabels extends Resource
{
    protected static ?string $label = 'Parent';
}

class ChildReadLabels extends ReadLabels
{
    public static function report(): ?string { return static::$label; }
}

class IntermediatePage extends Page {}

class CalledLegacyPage extends IntermediatePage
{
    protected function getActions(): array { return [Action::make('export')]; }
}

class ChildCallingLegacyPage extends CalledLegacyPage
{
    public function applicationActions(): array { return $this->getActions(); }
}

class ParentCalledLegacyPage extends IntermediatePage
{
    protected function getActions(): array { return [Action::make('export')]; }
}

class ChildCallingLegacyParentPage extends ParentCalledLegacyPage
{
    public function applicationActions(): array { return parent::getActions(); }
}

class IntermediateResource extends Resource {}

class GroupedParentLabels extends IntermediateResource
{
    protected static ?string $label = 'Parent';
}

class GroupedChildLabels extends GroupedParentLabels
{
    protected static ?string $label = 'Child', $other = null;
}

class GetterParentLabels extends Resource
{
    public static function getLabel(): ?string { return 'Parent'; }
}

class PropertyChildLabels extends GetterParentLabels
{
    protected static ?string $label = 'Child';
}

trait AdaptedActions
{
    protected function getActions(): array { return parent::getActions(); }
}

class AdaptedPage extends Page
{
    use AdaptedActions {
        getActions as applicationActions;
    }
}

trait MagicLabels
{
    public static function getLabel(): ?string { return __TRAIT__; }
}

class MagicResource extends Resource
{
    use MagicLabels;
}

trait ReadTraitLabels
{
    protected static ?string $label = 'Custom';

    public static function report(): ?string { return static::$label; }
}

class ReadTraitResource extends Resource
{
    use ReadTraitLabels;
}

class WrittenFilters extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        $this->filters['period'] = 'custom';

        return $this->filters;
    }
}

class RealFilters extends Widget
{
    use InteractsWithPageFilters;

    public array $filters = [];

    public function report(): array { return $this->filters; }
}

class ChildRealFilters extends RealFilters
{
    public function report(): array { return $this->filters; }
}

class CustomNames
{
    protected static ?string $label = 'Custom';

    protected function getActions(): array { return []; }
    protected function getCards(): array { return []; }
}
