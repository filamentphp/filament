<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Illuminate\Support\Collection;

class KeyValueEntry extends Entry implements HasEmbeddedView
{
    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('filament-infolists::components.entries.key_value.placeholder'));
    }

    public function keyLabel(string | Closure | null $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string | Closure | null $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `placeholder()` instead.
     */
    public function emptyMessage(string | Closure | null $message): static
    {
        $this->placeholder($message);

        return $this;
    }

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel) ?? __('filament-infolists::components.entries.key_value.columns.key.label');
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel) ?? __('filament-infolists::components.entries.key_value.columns.value.label');
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-key-value',
            ]);

        ob_start(); ?>

        <table <?= $attributes->toHtml() ?>>
            <thead>
                <tr>
                    <th scope="col">
                        <?= e($this->getKeyLabel()) ?>
                    </th>

                    <th scope="col">
                        <?= e($this->getValueLabel()) ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach (($state ?? []) as $key => $value) { ?>
                    <tr>
                        <td>
                            <?= e($key) ?>
                        </td>

                        <td>
                            <?= e($value) ?>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (empty($state)) { ?>
                    <tr>
                        <td colspan="2" class="fi-in-placeholder">
                            <?= e($this->getPlaceholder()) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
