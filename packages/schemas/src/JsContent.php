<?php

namespace Filament\Schemas;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class JsContent implements Htmlable
{
    protected string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public static function make(string $content): static
    {
        return app(static::class, ['content' => $content]);
    }

    public function toHtml(): string
    {
        $content = Js::from($this->content);

        return <<<HTML
            <span x-text="() => eval({$content})"></span>
        HTML;
    }
}
