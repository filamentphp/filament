<?php

return [

    'column_manager' => [

        'heading' => 'አምዶች',
    ],
    'columns' => [

        'actions' => [

            'label' => 'እርምጃ|እርምጃዎች',
        ],
        'text' => [

            'actions' => [

                'collapse_list' => 'ከ:count ያነሰ አሳይ',
                'expand_list' => 'ከ:count የበለጠ አሳይ',
            ],
            'more_list_items' => 'ከ:count በላይ',
        ],
    ],
    'fields' => [

        'bulk_select_page' => [

            'label' => 'ለጅምላ ድርጊት ሁሉንም ይምረጡ/አይምረጡ።',
        ],
        'bulk_select_record' => [

            'label' => ':keyን ለጅምላ ድርጊት ይምረጡ/አይምረጡ።',
        ],
        'bulk_select_group' => [

            'label' => 'ቡድን :titleን ለጅምላ ድርጊት ይምረጡ/አይምረጡ።',
        ],
        'search' => [

            'label' => 'ፈልግ',
            'placeholder' => 'ፈልግ',
            'indicator' => 'ፈልግ',
        ],
    ],
    'summary' => [

        'heading' => 'ማጠቃለያ',
        'subheadings' => [

            'all' => 'ሁሉም :label',
            'group' => 'የ:group ማጠቃለያ',
            'page' => 'የዚህ ገጽ',
        ],
        'summarizers' => [

            'average' => [

                'label' => 'አማካኝ',
            ],
            'count' => [

                'label' => 'ብዛት',
            ],
            'sum' => [

                'label' => 'ድምር',
            ],
        ],
    ],
    'actions' => [

        'disable_reordering' => [

            'label' => 'መዝገቦችን እንደገና መደርደር ጨርስ',
        ],
        'enable_reordering' => [

            'label' => 'እንደገና መደርደር',
        ],
        'filter' => [

            'label' => 'አጣራ',
        ],
        'group' => [

            'label' => 'ቡድን',
        ],
        'open_bulk_actions' => [

            'label' => 'የጅምላ ድርጊቶች',
        ],
        'column_manager' => [

            'label' => 'ዓምዶችን ቀያይር',
        ],
    ],
    'empty' => [

        'heading' => 'ምንም :model የሉም',
        'description' => 'ለመጀመር አንድ :model ይፍጠሩ.',
    ],
    'filters' => [

        'actions' => [

            'apply' => [

                'label' => 'ማጣርያዎችን ተግብር',
            ],
            'remove' => [

                'label' => 'ማጣሪያን ያስወግዱ',
            ],
            'remove_all' => [

                'label' => 'ሁሉንም ማጣሪያዎች ያስወግዱ',
                'tooltip' => 'ሁሉንም ማጣሪያዎች ያስወግዱ',
            ],
            'reset' => [

                'label' => 'ዳግም አስጀምር',
            ],
        ],
        'heading' => 'ማጣርያዎች',
        'indicator' => 'የተተገበሩ ማጣርያዎች',
        'multi_select' => [

            'placeholder' => 'ሁሉም',
        ],
        'select' => [

            'placeholder' => 'ሁሉም',
        ],
        'trashed' => [

            'label' => 'የተሰረዙ መዝገቦች',
            'only_trashed' => 'የተሰረዙ መዝገቦች ብቻ',
            'with_trashed' => 'ከተሰረዙ መዝገቦች ጋር',
            'without_trashed' => 'ያለ የተሰረዙ መዝገቦች',
        ],
    ],
    'grouping' => [

        'fields' => [

            'group' => [

                'label' => 'መድብ',
                'placeholder' => 'መድብ',
            ],
            'direction' => [

                'label' => 'የቡድን አቅጣጫ',
                'options' => [

                    'asc' => 'ከትንሽ ወደ ትልቅ',
                    'desc' => 'ከትልቅ ወደ ትንሽ',
                ],
            ],
        ],
    ],
    'reorder_indicator' => 'መዝገቦቹን ይጎትቱ እና በቅደም ተከተል ያስቀምጡ።',
    'selection_indicator' => [

        'selected_count' => '1 መዝገብ ተመርጧል|:count መዝገቦች ተመርጠዋል',
        'actions' => [

            'select_all' => [

                'label' => 'ሁሉንም :countውንም ምረጥ',
            ],
            'deselect_all' => [

                'label' => 'ሁሉንም አይምረጡ',
            ],
        ],
    ],
    'sorting' => [

        'fields' => [

            'column' => [

                'label' => 'ቅደምተከተል',
            ],
            'direction' => [

                'label' => 'የቅደም ተከተል አቅጣጫ',
                'options' => [

                    'asc' => 'ከትንሽ ወደ ትልቅ',
                    'desc' => 'ከትልቅ ወደ ትንሽ',
                ],
            ],
        ],
    ],
    'default_model_label' => 'ሪከርድ',
];
