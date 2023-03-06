<?php

class Page extends \Filament\Pages\Page
{
    protected function getTitle(): string
    {
        return null;
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getTitle(): string
    {
        return null;
    }
}

?>
