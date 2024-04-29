<?php

namespace Filament\Resources\Concerns;

use Filament\Schema\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait InteractsWithRelationshipTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }

    protected static string $relationship;

    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    protected static ?string $relatedResource = null;

    public static function getRelatedResource(): ?string
    {
        return static::$relatedResource;
    }

    public static function checkPolicyExistence(bool $condition = true): void
    {
        static::$shouldCheckPolicyExistence = $condition;
    }

    public static function skipAuthorization(bool $condition = true): void
    {
        static::$shouldSkipAuthorization = $condition;
    }

    public static function shouldCheckPolicyExistence(): bool
    {
        return static::$shouldCheckPolicyExistence;
    }

    public static function shouldSkipAuthorization(): bool
    {
        return static::$shouldSkipAuthorization;
    }

    public function getRelationship(): Relation | Builder
    {
        return $this->getOwnerRecord()->{static::getRelationshipName()}();
    }

    public static function getRelationshipName(): string
    {
        if (isset(static::$relationship)) {
            return static::$relationship;
        }

        return static::getRelatedResource()::getParentResourceRegistration()->getRelationshipName();
    }

    public function configureForm(Schema $form): void
    {
        $form->columns(2);

        if (static::getRelatedResource()) {
            static::getRelatedResource()::form($form);
        }

        $this->form($form);
    }

    public function configureInfolist(Schema $infolist): void
    {
        $infolist->columns(2);

        if (static::getRelatedResource()) {
            static::getRelatedResource()::infolist($infolist);
        }

        $this->infolist($infolist);
    }

    protected function makeTable(): Table
    {
        $table = $this->makeBaseTable()
            ->relationship(fn (): Relation | Builder => $this->getRelationship())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->queryStringIdentifier(Str::lcfirst(class_basename(static::class)))
            ->recordAction(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    if ($action->getUrl()) {
                        continue;
                    }

                    return $action->getName();
                }

                return null;
            })
            ->recordUrl(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    $url = $action->getUrl();

                    if (! $url) {
                        continue;
                    }

                    return $url;
                }

                return null;
            })
            ->authorizeReorder(fn (): bool => $this->canReorder());

        if ($relatedResource = static::getRelatedResource()) {
            $relatedResource::configureTable($table);
        }

        return $table;
    }
}
