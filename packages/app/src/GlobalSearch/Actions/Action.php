<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\StaticAction;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\Concerns\CanOpenUrl;

class Action extends StaticAction
{
    use CanBeOutlined;
    use CanEmitEvent;
    use CanOpenUrl;

    protected string $view = 'filament::global-search.actions.link-action';

    public function button(): static
    {
        $this->view('filament::global-search.actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament::global-search.actions.link-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament::global-search.actions.icon-button-action');

        return $this;
    }
}
