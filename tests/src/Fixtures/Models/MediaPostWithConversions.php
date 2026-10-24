<?php

namespace Filament\Tests\Fixtures\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPostWithConversions extends MediaPost
{
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->format('webp');
    }
}
