<?php

class Page extends \Filament\Pages\Page
{
    protected function getSubheading(): ?string
    {
        return null;
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getSubheading(): ?string
    {
        return null;
    }
}

?>
