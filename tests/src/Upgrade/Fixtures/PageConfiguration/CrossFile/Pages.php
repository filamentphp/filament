<?php

namespace Filament\Tests\Upgrade\Fixtures\StickyCrossFile;

use Filament\Pages\SimplePage;

class ParentPage extends SimplePage
{
    use NestedConfiguration;
}

class ChildPage extends ParentPage
{
    public function configure(bool $condition): void
    {
        parent::$formActionsAreSticky = $condition;
    }
}

function configure(ChildPage $page): void
{
    ChildPage::$formActionsAreSticky = false;
    $page::$formActionsAreSticky = true;
}
