<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;

class ManageUserSettings extends Page
{
    protected static string $resource = UserResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected string $view = 'filament.resources.user-resource.pages.manage-user-settings';
}
