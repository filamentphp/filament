<?php

namespace Filament\Forms\Components\Concerns;

trait CanChangeClass
{
    protected $addClasses;
    protected $removeClasses;

    public function getChangedClasses($currentClasses = '')
    {
        if(is_string($this->removeClasses)) {
            $classes = array_merge(
                explode(' ', trim($currentClasses)),
                explode(' ', trim($this->addClasses)),
            );
            $removeClasses = explode(' ', trim($this->removeClasses));

            $classes = array_filter($classes, function($item) use ($removeClasses) {
                return !in_array($item, $removeClasses);
            });

            return implode(' ', $classes);
        }
        elseif(is_bool($this->removeClasses) && $this->removeClasses) {
            return $this->addClasses;
        }


        return $currentClasses . ' ' . $this->addClasses;
    }

    /**
     * @param string $addClasses
     * @param string|boolean $removeClasses
     *
     * @return $this
     */
    public function changeClass($addClasses = '', $removeClasses = '')
    {
        $this->configure(function () use ($addClasses, $removeClasses) {
            $this->addClasses = $addClasses;
            $this->removeClasses = $removeClasses;
        });

        return $this;
    }
}
