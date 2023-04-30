<?php

class Page extends \Filament\Pages\Page
{
    protected function getHeading(): string
    {
        return null;
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getHeading(): string
    {
        return null;
    }
}

?>
