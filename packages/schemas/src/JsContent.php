<?php

namespace Filament\Schemas;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class JsContent implements Htmlable
{
    // Security: This class evaluates its content as JavaScript via
    // `eval()` in the browser. Only use with developer-defined
    // expressions — never with user input.

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
