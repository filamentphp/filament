<?php

return [

    'column_manager' => [

        'heading' => 'Sarakkeet',

        'actions' => [

            'apply' => [
                'label' => 'Aseta sarakkeet',
            ],

            'reorder' => [
                'label' => 'Uudelleenjärjestele sarake',
            ],

            'reset' => [
                'label' => 'Palauta',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Toiminta|Toiminnat',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Kyllä',
                'false' => 'Ei',
            ],

        ],

        'select' => [

            'loading_message' => 'Ladataan...',

            'no_options_message' => 'Ei vaihtoehtoja saatavilla.',

            'no_search_results_message' => 'Ei hakuasi vastaavia vaihtoehtoja.',

            'placeholder' => 'Valitse vaihtoehto',

            'searching_message' => 'Haetaan...',

            'search_prompt' => 'Aloita kirjoittaminen hakeaksesi...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Näytä :count vähemmän',
                'expand_list' => 'Näytä :count lisää',
            ],

            'more_list_items' => 'ja :count lisää',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Aseta/poista massatoiminnon valinta kaikista kohteista.',
        ],

        'bulk_select_record' => [
            'label' => 'Aseta/poista massatoiminnon valinta kohteelle :key.',
        ],

        'bulk_select_group' => [
            'label' => 'Aseta/poista massatoiminnon valinta ryhmälle :title.',
        ],

        'search' => [
            'label' => 'Haku',
            'placeholder' => 'Hae',
            'indicator' => 'Haku',
        ],

    ],

    'summary' => [

        'heading' => 'Yhteenveto',

        'subheadings' => [
            'all' => 'Kaikki :label',
            'group' => ':group yhteenveto',
            'page' => 'Tämä sivu',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Keskiarvo',
            ],

            'count' => [
                'label' => 'Määrä',
            ],

            'sum' => [
                'label' => 'Summa',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Viimeistele tietueiden järjestely',
        ],

        'enable_reordering' => [
            'label' => 'Järjestele tietueita',
        ],

        'reorder_record' => [
            'label' => 'Uudelleenjärjestele kohde :key',
        ],

        'filter' => [
            'label' => 'Suodata',
        ],

        'group' => [
            'label' => 'Ryhmä',
        ],

        'open_bulk_actions' => [
            'label' => 'Avaa toiminnot',
        ],

        'column_manager' => [
            'label' => 'Näytä sarakkeet',
        ],

        'toggle_record_content' => [
            'label' => 'Laajenna/kutista kohde :key',
        ],

    ],

    'empty' => [

        'heading' => 'Ei :model',

        'description' => 'Luo :model aloittaaksesi.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aseta suodattimet',
            ],

            'remove' => [
                'label' => 'Poista suodatin',
            ],

            'remove_all' => [
                'label' => 'Tyhjennä suodattimet',
                'tooltip' => 'Tyhjennä suodattimet',
            ],

            'reset' => [
                'label' => 'Palauta',
            ],

        ],

        'heading' => 'Suodattimet',

        'indicator' => 'Aktiiviset suodattimet',

        'multi_select' => [
            'placeholder' => 'Kaikki',
        ],

        'select' => [

            'placeholder' => 'Kaikki',

            'relationship' => [
                'empty_option_label' => 'Ei yhtään',
            ],

        ],

        'trashed' => [

            'label' => 'Poistetut tietueet',

            'only_trashed' => 'Vain poistetut tietueet',

            'with_trashed' => 'Poistettujen tietueiden kanssa',

            'without_trashed' => 'Ilman poistettuja tietueita',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Ryhmittele',
            ],

            'direction' => [

                'label' => 'Ryhmitys',

                'options' => [
                    'asc' => 'Nousevasti',
                    'desc' => 'Laskevasti',
                ],

            ],

        ],

    ],

    'loading' => 'Ladataan...',

    'reorder_indicator' => 'Raahaa ja pudota tietueet järjestykseen.',

    'result_count' => '{0} Ei tuloksia|{1} :count tulos|[2,*] :count tulosta',

    'selection_indicator' => [

        'selected_count' => '1 tietue valittu|:count tietuetta valittu',

        'actions' => [

            'select_all' => [
                'label' => 'Valitse kaikki :count tietuetta',
            ],

            'deselect_all' => [
                'label' => 'Poista valinnat',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Järjestele',
            ],

            'direction' => [

                'label' => 'Järjestys',

                'options' => [
                    'asc' => 'Nousevasti',
                    'desc' => 'Laskevasti',
                ],

            ],

        ],

    ],

    'default_model_label' => 'tietue',

];
