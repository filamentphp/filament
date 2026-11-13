<?php

return [

    'column_manager' => [

        'heading' => 'Stulpeliai',

        'position' => 'Pozicija :position iš :count.',

        'position_in_group' => 'Pozicija :position iš :count grupėje :group.',

        'reordered' => ':column perkeltas į poziciją :position iš :count.',

        'reordered_deferred' => ':column perkeltas į poziciją :position iš :count. Pritaikykite stulpelius, kad pakeitimai būtų išsaugoti.',

        'reordered_in_group' => ':column perkeltas į poziciją :position iš :count grupėje :group.',

        'reordered_in_group_deferred' => ':column perkeltas į poziciją :position iš :count grupėje :group. Pritaikykite stulpelius, kad pakeitimai būtų išsaugoti.',

        'actions' => [

            'apply' => [
                'label' => 'Pritaikyti stulpelius',
            ],

            'move_up' => [
                'label' => 'Perkelti :column aukštyn',
            ],

            'move_down' => [
                'label' => 'Perkelti :column žemyn',
            ],

            'reorder' => [
                'label' => 'Pertvarkyti stulpelį',
            ],

            'reset' => [
                'label' => 'Pirminiai stulpeliai',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Veiksmas|Veiksmai|Veiksmų',
        ],

        'editable' => [
            'saving' => 'Išsaugoma...',
            'saved' => 'Pakeitimai išsaugoti.',
            'failure' => 'Nepavyko patvirtinti išsaugojimo. Atnaujinkite puslapį ir bandykite dar kartą.',
            'unavailable' => 'Šio elemento nebegalima redaguoti.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Taip',
                'false' => 'Ne',
            ],

        ],

        'select' => [

            'loading_message' => 'Įkeliama...',

            'no_options_message' => 'Nėra galimų parinkčių.',

            'no_search_results_message' => 'Paieškos atitikmenų nėra.',

            'placeholder' => 'Pasirinkite parinktį',

            'searching_message' => 'Ieškoma...',

            'search_prompt' => 'Norėdami ieškoti pradėkite rašyti...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Slėpti :count',
                'expand_list' => 'Rodyti dar :count',
            ],

            'more_list_items' => 'ir dar :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pažymėti/atžymėti visus įrašus masiniam veiksmui.',
        ],

        'bulk_select_record' => [
            'label' => 'Pažymėti/atžymėti įrašą :key masiniam veiksmui.',
        ],

        'bulk_select_group' => [
            'label' => 'Pažymėti/atžymėti grupę :title masiniam veiksmui.',
        ],

        'search' => [
            'label' => 'Paieška',
            'placeholder' => 'Paieška',
            'indicator' => 'Paieška',
        ],

    ],

    'summary' => [

        'heading' => 'Santrauka',

        'subheadings' => [
            'all' => 'Viso :label',
            'group' => ':group santrauka',
            'page' => 'Šis puslapis',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Vidurkis',
            ],

            'count' => [
                'label' => 'Viso',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Baigti pertvarkyti įrašus',
        ],

        'enable_reordering' => [
            'label' => 'Pertvarkyti įrašus',
        ],

        'reorder_record' => [
            'label' => 'Pertvarkyti įrašą :key',
        ],

        'move_record_up' => [
            'label' => 'Perkelti :label aukštyn',
        ],

        'move_record_down' => [
            'label' => 'Perkelti :label žemyn',
        ],

        'filter' => [
            'label' => 'Filtras',
        ],

        'group' => [
            'label' => 'Grupė',
        ],

        'open_bulk_actions' => [
            'label' => 'Masiniai veiksmai',
        ],

        'column_manager' => [
            'label' => 'Stulpelių tvarkymas',
        ],

        'toggle_record_content' => [
            'label' => 'Išskleisti/sutraukti įrašą :key',
        ],

    ],

    'empty' => [

        'heading' => 'Nerasta įrašų',

        'description' => 'Norėdami pradėti, sukurkite įrašą.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Taikyti filtrus',
            ],

            'remove' => [
                'label' => 'Pašalinti filtrą',
            ],

            'remove_all' => [
                'label' => 'Pašalinti visus filtrus',
                'tooltip' => 'Pašalinti visus filtrus',
            ],

            'reset' => [
                'label' => 'Pirminiai filtrai',
            ],

        ],

        'heading' => 'Filtrai',

        'indicator' => 'Aktyvūs filtrai',

        'multi_select' => [
            'placeholder' => 'Visi',
        ],

        'select' => [
            'placeholder' => 'Visi',

            'relationship' => [
                'empty_option_label' => 'Nėra',
            ],
        ],

        'trashed' => [

            'label' => 'Ištrinti įrašai',

            'only_trashed' => 'Tik ištrinti įrašai',

            'with_trashed' => 'Su ištrintais įrašais',

            'without_trashed' => 'Be ištrintų įrašų',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupuoti pagal',
            ],

            'direction' => [

                'label' => 'Grupės kryptis',

                'options' => [
                    'asc' => 'Didėjančia tvarka',
                    'desc' => 'Mažėjančia tvarka',
                ],

            ],

        ],

    ],

    'loading' => 'Įkeliama...',

    'reorder_indicator' => 'Vilkite ir paleiskite įrašų rikiavimui.',

    'reordering' => [
        'position' => ':label, pozicija :position iš :count.',
        'moved' => ':label perkeltas į poziciją :position iš :count.',
    ],

    'result_count' => ':count rezultatas|:count rezultatai|:count rezultatų',

    'selection_indicator' => [

        'selected_count' => '1 įrašas pasirinktas|:count įrašai pasirinkti|:count įrašų pasirinkta',

        'actions' => [

            'select_all' => [
                'label' => 'Pažymėti visus :count',
            ],

            'deselect_all' => [
                'label' => 'Atžymėti visus',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Rūšiuoti pagal',
            ],

            'direction' => [

                'label' => 'Rūšiavimo kryptis',

                'options' => [
                    'asc' => 'Didėjimo tvarka',
                    'desc' => 'Mažėjimo tvarka',
                ],

            ],

        ],

    ],

    'default_model_label' => 'įrašas',
];
