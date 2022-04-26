<?php

namespace Filament\Support\Actions\Modal\Actions;

use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;

abstract class Action extends BaseAction
{
    use CanBeOutlined;
    use Concerns\CanCancelAction;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
}
