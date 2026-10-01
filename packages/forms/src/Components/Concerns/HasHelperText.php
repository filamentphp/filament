<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

trait HasHelperText
{
    protected ?Closure $helperTextSchema = null;

    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->belowContent($this->helperTextSchema = static function (Component $component) use ($text): ?Schema {
            $content = $component->evaluate($text);

            if (blank($content)) {
                return null;
            }

            $schema = $component->makeChildSchema(static::BELOW_CONTENT_SCHEMA_KEY)
                ->components([Text::make($content)]);

            if (filled($id = $component->getId())) {
                $schema->extraAttributes(['id' => e("{$id}-helper-text")], merge: true);
            }

            return $schema;
        });

        return $this;
    }

    public function getHelperTextId(): ?string
    {
        if (
            (! $this->helperTextSchema)
            || (($this->childComponents[static::BELOW_CONTENT_SCHEMA_KEY] ?? null) !== $this->helperTextSchema)
            || blank($this->getId())
            || ($this->getFieldWrapperView() === 'filament-forms::plain-field-wrapper')
        ) {
            return null;
        }

        $schema = $this->getChildSchema(static::BELOW_CONTENT_SCHEMA_KEY);

        if ((! $schema) || $schema->isDirectlyHidden() || (! count($schema->getComponents()))) {
            return null;
        }

        $id = $schema->getExtraAttributeBag()->get('id');

        return filled($id) ? html_entity_decode($id, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
    }
}
