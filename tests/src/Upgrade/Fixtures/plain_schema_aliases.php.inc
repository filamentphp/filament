<?php

namespace PlainSchemaAliases;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Schema;

class Owner
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public function fluent(): static { return $this; }

    public function cache(Schema $schema): void
    {
        $this->cacheForm('form', $schema);
        $this->cacheForm(form: null, name: 'form');
        $this->cacheInfolist(name: 'info', schema: $schema);
        $this->hasCachedForm('form');
        $this->hasCachedInfolist('info');
        $this->getOldFormState(statePath: 'data.name');
        $this->cacheForm('missing');
        $this->cacheForm(...$arguments);
        $callback = $this->cacheForm(...);
    }
}

function calls(Owner $owner, ?Owner $nullable, object $unrelated, string $method): void
{
    $owner->fluent()->getForm(name: 'form')->key('form');
    $nullable?->fluent()->getInfolist('info')?->key('info');
    $owner->getCachedForms();
    $owner->getCachedInfolists();
    $owner->isCachingForms();
    $owner->getActiveFormsLocale();
    $unrelated->getForm('form');
    $owner->{$method}('form');
}
-----
<?php

namespace PlainSchemaAliases;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Schema;

class Owner
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public function fluent(): static { return $this; }

    public function cache(Schema $schema): void
    {
        $this->cacheSchema('form', $schema);
        $this->cacheSchema(schema: null, name: 'form');
        $this->cacheSchema(name: 'info', schema: $schema);
        $this->hasCachedSchema('form');
        $this->hasCachedSchema('info');
        $this->getOldSchemaState(statePath: 'data.name');
        $this->cacheForm('missing');
        $this->cacheForm(...$arguments);
        $callback = $this->cacheForm(...);
    }
}

function calls(Owner $owner, ?Owner $nullable, object $unrelated, string $method): void
{
    $owner->fluent()->getSchema(name: 'form')->key('form');
    $nullable?->fluent()->getSchema('info')?->key('info');
    $owner->getCachedSchemas();
    $owner->getCachedSchemas();
    $owner->isCachingSchemas();
    $owner->getActiveSchemaLocale();
    $unrelated->getForm('form');
    $owner->{$method}('form');
}
