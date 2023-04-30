<?php

class Page extends \Filament\Pages\Page
{
    protected function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }
}

?>
-----
<?php

class Page extends \Filament\Pages\Page
{
    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }
}

?>
