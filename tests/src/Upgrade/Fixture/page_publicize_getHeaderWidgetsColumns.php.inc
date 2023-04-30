<?php

class Page extends \Filament\Pages\Page
{
    protected function getHeaderWidgetsColumns(): array
    {
        return [];
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getHeaderWidgetsColumns(): array
    {
        return [];
    }
}

?>
