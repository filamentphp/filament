<?php

namespace Filament\Tables\Columns;

class Icon extends Column
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $options = [];

    public function getOptions()
    {
        return $this->options;
    }

    public function options($options)
    {
        $this->configure(function () use ($options) {
            $this->options = $options;
        });

        return $this;
    }
}
