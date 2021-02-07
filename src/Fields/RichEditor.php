<?php

namespace Filament\Fields;

use Filament\Traits\FieldConcerns;

class RichEditor extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanHavePlaceholder;

    public $toolbarButtons = [
        'bold',
        'bullet',
        'code',
        'heading',
        'italic',
        'link',
        'number',
        'quote',
        'redo',
        'strike',
        'subHeading',
        'undo',
    ];

    public function disableToolbarButtons($buttonsToDisable)
    {
        if (! is_array($buttonsToDisable)) $buttonsToDisable = [$buttonsToDisable];

        $this->toolbarButtons = array_merge($this->toolbarButtons, $buttonsToDisable);

        $this->toolbarButtons = collect($this->toolbarButtons)
            ->filter(fn ($button) => ! in_array($button, $buttonsToDisable))
            ->toArray();

        return $this;
    }

    public function enableToolbarButtons($buttonsToEnable)
    {
        if (! is_array($buttonsToEnable)) $buttonsToEnable = [$buttonsToEnable];

        $this->toolbarButtons = array_merge($this->toolbarButtons, $buttonsToEnable);

        return $this;
    }

    public function hasToolbarButton($button)
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->toolbarButtons));
        }

        return in_array($button, $this->toolbarButtons);
    }

    public function toolbarButtons($buttons)
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }
}
