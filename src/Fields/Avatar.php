<?php

namespace Filament\Fields;

class Avatar extends Field {
    public $modelDirective = 'wire:model';
    public $avatar;
    public $user;
    public $buttonLabel = 'filament::avatar.change';
    public $deleteMethod;
    public $size = 64;

    /**
     * @return static
     */
    public function avatar($avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return static
     */
    public function user($user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return static
     */
    public function buttonLabel(string $label): self
    {
        $this->buttonLabel = $label;
        return $this;
    }

    /**
     * @return static
     */
    public function deleteMethod(string $method): self
    {
        $this->deleteMethod = $method;
        return $this;
    }

    /**
     * @return static
     */
    public function size($size): self
    {
        $this->size = $size;
        return $this;
    }
}