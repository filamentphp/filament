<?php

namespace Filament\Fields;

class Avatar extends Field {
    public $avatar;
    public $user;
    public $delete;
    public $size = 64;

    public function avatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function user($user)
    {
        $this->user = $user;
        return $this;
    }

    public function delete($delete)
    {
        $this->delete = $delete;
        return $this;
    }

    public function size($size)
    {
        $this->size = $size;
        return $this;
    }
}