<?php

namespace Filament\Fields;

class Avatar extends Field
{
    public $avatar;

    public $buttonLabel = 'filament::avatar.change';

    public $deleteMethod;

    public $modelDirective = 'wire:model';

    public $size = 64;

    public $user;

    public function avatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function buttonLabel(string $label)
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function deleteMethod(string $method)
    {
        $this->deleteMethod = $method;

        return $this;
    }

    public function size($size)
    {
        $this->size = $size;

        return $this;
    }

    public function user($user)
    {
        $this->user = $user;

        return $this;
    }
}
