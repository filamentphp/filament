<?php

return [

    'label' => 'Հարցման ձևավորիչ',

    'form' => [

        'operator' => [
            'label' => 'Օպերատոր',
        ],

        'or_groups' => [

            'label' => 'Խմբեր',

            'group' => [
                'label' => 'Խումբ',
            ],

            'block' => [
                'label' => '«Կամ» պայման',
                'picker_group' => 'Տրամաբանություն',
                'or' => 'կամ',
            ],

        ],

        'rules' => [

            'label' => 'Կանոններ',

            'item' => [
                'and' => 'և',
            ],

        ],

    ],

    'no_rules' => '(Կանոններ չկան)',

    'max_rules_reached_tooltip' => 'Դուք հասել եք կանոնների առավելագույն քանակին՝ :count։',

    'item_separators' => [
        'and' => 'և',
        'or' => 'կամ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Լրացված է',
                'inverse' => 'Դատարկ է',
            ],

            'summary' => [
                'direct' => ':attribute լրացված է',
                'inverse' => ':attribute դատարկ է',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ճիշտ է',
                    'inverse' => 'Սխալ է',
                ],

                'summary' => [
                    'direct' => ':attribute ճիշտ է',
                    'inverse' => ':attribute սխալ է',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Հետո է',
                    'inverse' => 'Հետո չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :date-ից հետո է',
                    'inverse' => ':attribute :date-ից հետո չէ',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Առաջ է',
                    'inverse' => 'Առաջ չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :date-ից առաջ է',
                    'inverse' => ':attribute :date-ից առաջ չէ',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Ամսաթիվ է',
                    'inverse' => 'Ամսաթիվ չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :date է',
                    'inverse' => ':attribute :date չէ',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Ամիս է',
                    'inverse' => 'Ամիս չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :month է',
                    'inverse' => ':attribute :month չէ',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Տարի է',
                    'inverse' => 'Տարի չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :year է',
                    'inverse' => ':attribute :year չէ',
                ],

            ],

            'unit_labels' => [
                'second' => 'Վայրկյաններ',
                'minute' => 'Րոպեներ',
                'hour' => 'Ժամեր',
                'day' => 'Օրեր',
                'week' => 'Շաբաթներ',
                'month' => 'Ամիսներ',
                'quarter' => 'Եռամսյակներ',
                'year' => 'Տարիներ',
            ],

            'presets' => [
                'past_decade' => 'Վերջին տասնամյակ',
                'past_5_years' => 'Վերջին 5 տարի',
                'past_2_years' => 'Վերջին 2 տարի',
                'past_year' => 'Վերջին տարի',
                'past_6_months' => 'Վերջին 6 ամիս',
                'past_quarter' => 'Վերջին եռամսյակ',
                'past_month' => 'Վերջին ամիս',
                'past_2_weeks' => 'Վերջին 2 շաբաթ',
                'past_week' => 'Վերջին շաբաթ',
                'past_hour' => 'Վերջին ժամ',
                'past_minute' => 'Վերջին րոպե',
                'this_decade' => 'Այս տասնամյակ',
                'this_year' => 'Այս տարի',
                'this_quarter' => 'Այս եռամսյակ',
                'this_month' => 'Այս ամիս',
                'today' => 'Այսօր',
                'this_hour' => 'Այս ժամ',
                'this_minute' => 'Այս րոպե',
                'next_minute' => 'Հաջորդ րոպե',
                'next_hour' => 'Հաջորդ ժամ',
                'next_week' => 'Հաջորդ շաբաթ',
                'next_2_weeks' => 'Հաջորդ 2 շաբաթ',
                'next_month' => 'Հաջորդ ամիս',
                'next_quarter' => 'Հաջորդ եռամսյակ',
                'next_6_months' => 'Հաջորդ 6 ամիս',
                'next_year' => 'Հաջորդ տարի',
                'next_2_years' => 'Հաջորդ 2 տարի',
                'next_5_years' => 'Հաջորդ 5 տարի',
                'next_decade' => 'Հաջորդ տասնամյակ',
                'custom' => 'Անհատական',
            ],

            'form' => [

                'date' => [
                    'label' => 'Ամսաթիվ',
                ],

                'month' => [
                    'label' => 'Ամիս',
                ],

                'year' => [
                    'label' => 'Տարի',
                ],

                'mode' => [

                    'label' => 'Ամսաթվի տեսակ',

                    'options' => [
                        'absolute' => 'Որոշակի ամսաթիվ',
                        'relative' => 'Սահող ժամանակահատված',
                    ],

                ],

                'preset' => [
                    'label' => 'Ժամանակահատված',
                ],

                'relative_value' => [
                    'label' => 'Քանակ',
                ],

                'relative_unit' => [
                    'label' => 'Ժամանակի միավոր',
                ],

                'tense' => [

                    'label' => 'Ժամանակ',

                    'options' => [
                        'past' => 'Անցյալ',
                        'future' => 'Ապագա',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Հավասար է',
                    'inverse' => 'Հավասար չէ',
                ],

                'summary' => [
                    'direct' => ':attribute հավասար է :number',
                    'inverse' => ':attribute հավասար չէ :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Առավելագույնն է',
                    'inverse' => 'Ավելի մեծ է, քան',
                ],

                'summary' => [
                    'direct' => ':attribute առավելագույնն է :number',
                    'inverse' => ':attribute ավելի մեծ է, քան :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Նվազագույն է',
                    'inverse' => 'Ավելի փոքր է, քան',
                ],

                'summary' => [
                    'direct' => ':attribute նվազագույն է :number',
                    'inverse' => ':attribute ավելի փոքր է, քան :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Միջին',
                    'summary' => 'Միջին :attribute',
                ],

                'max' => [
                    'label' => 'Առավելագույն',
                    'summary' => 'Առավելագույն :attribute',
                ],

                'min' => [
                    'label' => 'Նվազագույն',
                    'summary' => 'Նվազագույն :attribute',
                ],

                'sum' => [
                    'label' => 'Գումար',
                    'summary' => ':attribute-ի գումար',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Կուտակային',
                ],

                'number' => [
                    'label' => 'Թիվ',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ունի',
                    'inverse' => 'Չունի',
                ],

                'summary' => [
                    'direct' => 'Ունի :count :relationship',
                    'inverse' => 'Չունի :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ունի առավելագույնը',
                    'inverse' => 'Ունի առավել քան',
                ],

                'summary' => [
                    'direct' => 'Ունի առավելագույնը :count :relationship',
                    'inverse' => 'Ունի առավել քան :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ունի նվազագույնը',
                    'inverse' => 'Ունի պակաս քան',
                ],

                'summary' => [
                    'direct' => 'Ունի նվազագույնը :count :relationship',
                    'inverse' => 'Ունի պակաս քան :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Դատարկ է',
                    'inverse' => 'Դատարկ չէ',
                ],

                'summary' => [
                    'direct' => ':relationship դատարկ է',
                    'inverse' => ':relationship դատարկ չէ',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Հավասար է',
                        'inverse' => 'Հավասար չէ',
                    ],

                    'multiple' => [
                        'direct' => 'Պարունակում է',
                        'inverse' => 'Չի պարունակում',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values է',
                        'inverse' => ':relationship :values չէ',
                    ],

                    'multiple' => [
                        'direct' => ':relationship պարունակում է :values',
                        'inverse' => ':relationship չի պարունակում :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' կամ ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Արժեք',
                    ],

                    'values' => [
                        'label' => 'Արժեքներ',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Քանակ',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Հավասար է',
                    'inverse' => 'Հավասար չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :values է',
                    'inverse' => ':attribute :values չէ',
                    'values_glue' => [
                        ', ',
                        'final' => ' կամ ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Արժեք',
                    ],

                    'values' => [
                        'label' => 'Արժեքներ',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Պարունակում է',
                    'inverse' => 'Չի պարունակում',
                ],

                'summary' => [
                    'direct' => ':attribute պարունակում է :text',
                    'inverse' => ':attribute չի պարունակում :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Վերջանում է',
                    'inverse' => 'Չի վերջանում',
                ],

                'summary' => [
                    'direct' => ':attribute վերջանում է :text-ով',
                    'inverse' => ':attribute չի վերջանում :text-ով',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Հավասար է',
                    'inverse' => 'Հավասար չէ',
                ],

                'summary' => [
                    'direct' => ':attribute հավասար է :text-ով',
                    'inverse' => ':attribute հավասար չէ :text-ով',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Սկսվում է',
                    'inverse' => 'Չի սկսվում',
                ],

                'summary' => [
                    'direct' => ':attribute սկսվում է :text-ով',
                    'inverse' => ':attribute չի սկսվում :text-ով',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Տեքստ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Ավելացնել կանոն',
        ],

        'add_rule_group' => [
            'label' => 'Ավելացնել «կամ» խումբ',
        ],

    ],

];
