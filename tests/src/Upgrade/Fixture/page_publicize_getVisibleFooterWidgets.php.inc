<?php

class Page extends \Filament\Pages\Page
{
    protected function getVisibleFooterWidgets(): array
    {
        return [];
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getVisibleFooterWidgets(): array
    {
        return [];
    }
}

?>
