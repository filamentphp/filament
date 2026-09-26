<?php

namespace Filament\Forms\Components\RichEditor;

use Faker\Provider\Base;

class RichContentFakerProvider extends Base
{
    public function filamentRichContent(?RichContentAttribute $attribute = null): RichContentFaker
    {
        return RichContentFaker::make($this->generator, $attribute);
    }
}
