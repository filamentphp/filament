<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedAffixMethods;

use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;

function configure(TextInput $input, ?Select $select, ColorPicker $color, DateTimePicker $date, TagsInput $tags, Htmlable $html, array $arguments): void
{
    $input->postfix(label: null, isInline: false);
    $input->postfix(fn () => 'kg', fn () => true);
    $select?->postfix($html);
    $color->postfix('hex');
    $date->postfix('UTC');
    $tags->postfix(...$arguments);
    $callback = $input->postfix(...);
    TextInput::make('weight')->postfix('kg')->reactive();
    $input->suffix('canonical');
}

class CustomInput extends TextInput
{
    public function postfix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        return parent::postfix($label, $isInline);
    }
}

class InheritedCustomInput extends CustomInput {}

class CustomSuffix extends TextInput
{
    public function suffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        return $this;
    }
}

function manual(InheritedCustomInput $custom, CustomSuffix $suffix, TextInput $input, object $unknown): void
{
    $custom->postfix('kg');
    $suffix->postfix('kg');
    $suffix->label('Weight')->postfix('kg');
    $input->{'postfix'}('kg');
    $callback = [$input, 'postfix'];
    $unknown->postfix('kg');
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedAffixMethods;

use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;

function configure(TextInput $input, ?Select $select, ColorPicker $color, DateTimePicker $date, TagsInput $tags, Htmlable $html, array $arguments): void
{
    $input->suffix(label: null, isInline: false);
    $input->suffix(fn () => 'kg', fn () => true);
    $select?->suffix($html);
    $color->suffix('hex');
    $date->suffix('UTC');
    $tags->suffix(...$arguments);
    $callback = $input->suffix(...);
    TextInput::make('weight')->suffix('kg')->live();
    $input->suffix('canonical');
}

class CustomInput extends TextInput
{
    public function postfix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        return parent::postfix($label, $isInline);
    }
}

class InheritedCustomInput extends CustomInput {}

class CustomSuffix extends TextInput
{
    public function suffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        return $this;
    }
}

function manual(InheritedCustomInput $custom, CustomSuffix $suffix, TextInput $input, object $unknown): void
{
    $custom->postfix('kg');
    $suffix->postfix('kg');
    $suffix->label('Weight')->postfix('kg');
    $input->{'postfix'}('kg');
    $callback = [$input, 'postfix'];
    $unknown->postfix('kg');
}
