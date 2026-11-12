<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\EditConflictActions;
use Filament\Tests\Fixtures\Models\Post;

class EditConflictBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(EditConflictActions::class, [
                'record' => Post::findOrFail(request()->query('record')),
            ])->key('editor'),
        ]);
    }
}
