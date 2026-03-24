<?php

namespace App\Filament\Resources\PostResource\Resources\CommentResource\Pages;

use App\Filament\Resources\PostResource\Resources\CommentResource\CommentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;
}
