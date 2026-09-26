<?php

namespace Filament\Forms;

use Faker\Provider\Base;
use Filament\Forms\Components\MarkdownEditor\MarkdownFaker;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentFaker;

class FakerProvider extends Base
{
    public function filamentMarkdown(): MarkdownFaker
    {
        return MarkdownFaker::make($this->generator);
    }

    public function filamentRichContent(?RichContentAttribute $attribute = null): RichContentFaker
    {
        return RichContentFaker::make($this->generator, $attribute);
    }
}
