<?php

namespace Filament\Support\Attributes;

use Attribute;
use Livewire\Attribute as LivewireAttribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ServerOnly extends LivewireAttribute {}
