<?php

use Filament\Http\Livewire\Concerns\CanNotify as LegacyNotifications;

class RemovesSingleTrait
{
    use LegacyNotifications;
}

class PreservesOtherTraits
{
    use \Filament\Resources\Pages\ListRecords\Concerns\CanCreateRecords, \App\Concerns\KeepsBehavior, LegacyNotifications;

    use \App\Concerns\KeepsOtherBehavior;
}

trait RemovesNestedTrait
{
    use \Filament\Resources\RelationManagers\Concerns\CanAttachRecords;
}

$component = new class
{
    use LegacyNotifications;
};

class LeavesUnrelatedTraitAlone
{
    use \App\Concerns\CanNotify;
}

?>
-----
<?php

use Filament\Http\Livewire\Concerns\CanNotify as LegacyNotifications;

class RemovesSingleTrait
{
}

class PreservesOtherTraits
{
    use \App\Concerns\KeepsBehavior;

    use \App\Concerns\KeepsOtherBehavior;
}

trait RemovesNestedTrait
{
}

$component = new class
{
};

class LeavesUnrelatedTraitAlone
{
    use \App\Concerns\CanNotify;
}

?>
