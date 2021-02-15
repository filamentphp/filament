<x-forms::tabs :id="$field->id" :label="__($field->label)" :tabs="$field->getTabsConfig()">
    @foreach($field->fields as $tab)
        {{ $tab->render() }}
    @endforeach
</x-forms::tabs>
