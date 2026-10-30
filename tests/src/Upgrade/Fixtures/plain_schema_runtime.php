<?php

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Container\Container;
use Livewire\Component;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('Schema cache migration changed behavior');
    }
}

class Owner extends Component implements HasSchemas
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public int $builds = 0;

    public function details(Schema $schema): Schema
    {
        $this->builds++;

        return $schema;
    }

    public function inspect(): void
    {
        $schema = new Schema($this);
        check($this->cacheForm(name: 'details', form: $schema) === $schema);
        check($this->hasCachedForm('details'));
        check($this->cacheForm(form: null, name: 'details') === null);
        check(! $this->hasCachedForm('details'));
        check($this->builds === 0);
        check($this->cacheSchema('details') instanceof Schema);
        check($this->builds === 1);
        check($this->cacheForm('details', static fn (): Schema => $schema) === $schema);
        check($this->cacheInfolist(name: 'info', schema: $schema) === $schema);
        check($this->hasCachedInfolist('info'));
    }
}

Container::setInstance(new Container);
(new Owner)->inspect();
echo "PASS\n";
