<?php

class Page extends \Filament\Pages\Page
{
    protected function getBreadcrumbs(): array
    {
        return [];
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getBreadcrumbs(): array
    {
        return [];
    }
}

?>
