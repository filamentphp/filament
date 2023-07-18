<?php

class Page extends \Filament\Pages\Page
{
    protected function getVisibleHeaderWidgets(): array
    {
        return [];
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getVisibleHeaderWidgets(): array
    {
        return [];
    }
}

?>
