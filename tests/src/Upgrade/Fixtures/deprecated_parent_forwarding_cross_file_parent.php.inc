<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingCrossFile;

use Closure;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Support\Arrayable;

class ParentIcon extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }
}

class ParentAction extends Action
{
    public function form(array | Closure | null $customSchema): static
    {
        return parent::form(form: $customSchema);
    }
}
