<?php

namespace Filament\Schemas\Contracts;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\TranslatableContentDriver;

interface HasSchemas
{
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver;

    public function getOldSchemaState(string $statePath): mixed;

    /**
     * @param  array<Component>  $skipComponentsChildContainersWhileSearching
     */
    public function getSchemaComponent(string $key, bool $withHidden = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null;

    public function getSchema(string $name): ?Schema;

    public function currentlyValidatingSchema(?Schema $schema): void;

    public function getDefaultTestingSchemaName(): ?string;
}
