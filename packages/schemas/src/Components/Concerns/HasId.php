<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait HasId
{
    protected string | Closure | null $id = null;

    public function id(string | Closure | null $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->getCustomId() ?? $this->getKey();
    }

    public function getCustomId(): ?string
    {
        $id = $this->evaluate($this->id);

        // Security: Strip characters that could break out of a quoted HTML attribute or a JS string, so every downstream sink that embeds this ID raw is safe without per-sink escaping. Legitimate IDs never contain these characters.
        return ($id === null) ? null : preg_replace('/[<>"\'`\x00-\x1F\x7F]/', '', $id);
    }
}
