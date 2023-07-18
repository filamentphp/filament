<?php

class Page extends \Filament\Pages\Page
{
    protected function getFooterWidgetsColumns(): array
    {
        return [];
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getFooterWidgetsColumns(): array
    {
        return [];
    }
}

?>
