<?php

namespace Filament\Resources\Forms\Actions;

class Button
{
    protected $action;

    protected $color = 'white';

    protected $label;

    protected $type = 'button';

    protected $url;

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public static function make($label)
    {
        return (new static())->label($label);
    }

    public function primary()
    {
        $this->color = 'primary';

        return $this;
    }

    public function submit()
    {
        $this->type = 'submit';

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}
