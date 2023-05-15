<?php

namespace Filament\Infolists\Contracts;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Contracts\TranslatableContentDriver;
use Livewire\TemporaryUploadedFile;

interface HasInfolists extends HasForms
{
    public function getInfolist(string $name): ?Infolist;
}
