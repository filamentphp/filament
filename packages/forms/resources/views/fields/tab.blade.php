<x-filament::tab-content :id="$field->parentField->id . '.' . $field->id">
    <x-filament::subform :fields="$field->fields" />
</x-filament::tab-content>
