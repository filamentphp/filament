<?php

namespace Filament\Support\Actions\Modal\Actions;

use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanSubmitForm;

abstract class Action extends BaseAction
{
    use CanBeOutlined;
    use CanSubmitForm;
    use Concerns\CanCancelAction;
    use Concerns\HasAction;
}
