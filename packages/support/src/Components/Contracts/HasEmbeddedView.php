<?php

namespace Filament\Support\Components\Contracts;

interface HasEmbeddedView
{
    public function toEmbeddedHtml(): string;
}
