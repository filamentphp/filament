<?php

namespace Filament\Resources;

use Filament\Filament;
use Filament\Resources\Forms\Form;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;

class SubResourceManager extends Component
{
    public static $cancelButtonLabel = 'filament::resources/pages/edit-record.buttons.cancel.label';

    public static $saveButtonLabel = 'filament::resources/pages/edit-record.buttons.save.label';

    public static $savedMessage = 'filament::resources/pages/edit-record.messages.saved';

    public $owner;

    public static $relationship;

    public function getModel()
    {
        return $this->getQuery()->first();
    }

    public function getQuery()
    {
        return $this->getRelationship();
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getRelationship()
    {
        return $this->owner->{static::getRelationshipName()}();
    }

    public static function getRelationshipName()
    {
        return static::$relationship;
    }

    protected static $components = [
        'edit' => SubResourceManager\EditRecord::class,
        // 'list' => SubResourceManager\ListRecords::class,
    ];

    public function canUse()
    {
        if (
        $this->isType(Relations\hasOne::class)
        ) {
            return true;
        }

        return false;
    }

    public function canCreate()
    {
        return Filament::can('create', $this->getModel());
    }

    public function canDelete()
    {
        return true;
    }

    public static function getComponent($component)
    {
        return Livewire::getAlias(static::$components[$component]);
    }

    public static function getPrimaryColumn()
    {
        return property_exists(static::class, 'primaryColumn') && static::$primaryColumn !== '' ?
            static::$primaryColumn :
            null;
    }

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

    public function isType($type)
    {
        return $this->getRelationship() instanceof $type;
    }

    public function render()
    {
        return view('filament::subResource-manager');
    }
}
