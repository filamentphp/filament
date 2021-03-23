<?php

namespace Filament\Resources;

use Filament\Filament;
use Filament\Resources\RelationManager;
use Illuminate\Database\Eloquent\Relations;
use Livewire\Component;
use Livewire\Livewire;

class RelationManager extends Component
{
    public static $attachButtonLabel = 'filament::resources/relation-manager.buttons.attach.label';

    public static $attachModalAttachAnotherButtonLabel = 'filament::resources/relation-manager.modals.attach.buttons.attachAnother.label';

    public static $attachModalAttachButtonLabel = 'filament::resources/relation-manager.modals.attach.buttons.attach.label';

    public static $attachModalAttachedMessage = 'filament::resources/relation-manager.modals.attach.messages.attached';

    public static $attachModalCancelButtonLabel = 'filament::resources/relation-manager.modals.attach.buttons.cancel.label';

    public static $attachModalHeading = 'filament::resources/relation-manager.modals.attach.heading';

    public static $createButtonLabel = 'filament::resources/relation-manager.buttons.create.label';

    public static $createModalCancelButtonLabel = 'filament::resources/relation-manager.modals.create.buttons.cancel.label';

    public static $createModalCreateAnotherButtonLabel = 'filament::resources/relation-manager.modals.create.buttons.createAnother.label';

    public static $createModalCreateButtonLabel = 'filament::resources/relation-manager.modals.create.buttons.create.label';

    public static $createModalCreatedMessage = 'filament::resources/relation-manager.modals.create.messages.created';

    public static $createModalHeading = 'filament::resources/relation-manager.modals.create.heading';

    public static $detachButtonLabel = 'filament::resources/relation-manager.buttons.detach.label';

    public static $detachModalCancelButtonLabel = 'filament::resources/relation-manager.modals.detach.buttons.cancel.label';

    public static $detachModalDescription = 'filament::resources/relation-manager.modals.detach.description';

    public static $detachModalDetachButtonLabel = 'filament::resources/relation-manager.modals.detach.buttons.detach.label';

    public static $detachModalDetachedMessage = 'filament::resources/relation-manager.modals.detach.messages.detached';

    public static $detachModalHeading = 'filament::resources/relation-manager.modals.detach.heading';

    public static $editModalCancelButtonLabel = 'filament::resources/relation-manager.modals.edit.buttons.cancel.label';

    public static $editModalHeading = 'filament::resources/relation-manager.modals.edit.heading';

    public static $editModalSaveButtonLabel = 'filament::resources/relation-manager.modals.edit.buttons.save.label';

    public static $editModalSavedMessage = 'filament::resources/relation-manager.modals.edit.messages.saved';

    public static $editRecordActionLabel = 'filament::resources/pages/list-records.table.recordActions.edit.label';

    protected static $components = [
        'attach' => RelationManager\AttachRecord::class,
        'create' => RelationManager\CreateRecord::class,
        'edit' => RelationManager\EditRecord::class,
        'list' => RelationManager\ListRecords::class,
    ];

    public $owner;

    public static $relationship;

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) {
            return static::$title;
        }
        return (string) Str::of(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getComponent($component)
    {
        return Livewire::getAlias(static::$components[$component]);
    }

    public function canAttach()
    {
        if (
            $this->isType(Relations\HasMany::class) ||
            $this->isType(Relations\MorphMany::class)
        ) {
            return false;
        }

        return true;
    }

    public function canCreate()
    {
        return Filament::can('create', $this->getModel());
    }

    public function canDelete()
    {
        return true;
    }

    public function canDetach()
    {
        if (
            $this->isType(Relations\HasMany::class) ||
            $this->isType(Relations\MorphMany::class)
        ) {
            return false;
        }

        return true;
    }

    public function getModel()
    {
        return $this->getQuery()->getModel();
    }

    public static function getPrimaryColumn()
    {
        return property_exists(static::class, 'primaryColumn') && static::$primaryColumn !== '' ?
            static::$primaryColumn :
            null;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getQuery()
    {
        return $this->getRelationship();
    }

    public function getRelationship()
    {
        return $this->owner->{static::getRelationshipName()}();
    }

    public static function getRelationshipName()
    {
        return static::$relationship;
    }

    public function isType($type)
    {
        return $this->getRelationship() instanceof $type;
    }

    public function render()
    {
        return view('filament::relation-manager');
    }
}
