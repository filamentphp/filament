<?php

namespace Filament\Navigation;

class UnlabelledNavigationGroup extends NavigationGroup
{
    protected ?string $label = '';

    protected bool $collapsible = false;

    protected bool $collapsed = false;
}
