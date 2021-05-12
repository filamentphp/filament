<?php

namespace Filament\Forms\Components\Concerns;

trait CanChangeClass
{
    protected $addClasses;
    protected $removedClasses;

    public function getChangedClasses($currentClasses = '')
    {
        $classes = array_merge(
            explode(' ', trim($currentClasses)),
            explode(' ', trim($this->addClasses)),
        );
        $removeClasses = explode(' ', trim($this->removedClasses));

        $classes = array_filter($classes, function($item) use ($removeClasses) {
            return !in_array($item, $removeClasses);
        });

        return implode(' ', $classes);
    }

    public function changeClass($addClasses = '', $removedClasses = '')
    {
        $this->configure(function () use ($addClasses, $removedClasses) {
            $this->addClasses = $addClasses;
            $this->removedClasses = $removedClasses;
        });

        return $this;
    }
}
