<?php

namespace Filament\Forms\Components;

class MarkdownEditor extends Field
{
    use Concerns\HasPlaceholder;
    use Concerns\CanBeAutofocused;

    protected $boldLabel = 'forms::fields.markdown.bold';

    protected $italicLabel = 'forms::fields.markdown.italic';

    protected $strikethroughLabel = 'forms::fields.markdown.strikethrough';

    protected $urlLabel = 'forms::fields.markdown.url';

    protected $imageLabel = 'forms::fields.markdown.image';

    protected $codeLabel = 'forms::fields.markdown.code';

    protected $unorderedListLabel = 'forms::fields.markdown.unorderedList';

    protected $orderedListLabel = 'forms::fields.markdown.orderedList';

    public function getBoldLabel()
    {
        return $this->boldLabel;
    }

    public function getItalicLabel()
    {
        return $this->italicLabel;
    }

    public function getStrikethroughLabel()
    {
        return $this->strikethroughLabel;
    }

    public function getUrlLabel()
    {
        return $this->urlLabel;
    }

    public function getImageLabel()
    {
        return $this->imageLabel;
    }

    public function getCodeLabel()
    {
        return $this->codeLabel;
    }

    public function getUnorderedListLabel()
    {
        return $this->unorderedListLabel;
    }

    public function getOrderedListLabel()
    {
        return $this->orderedListLabel;
    }
}
