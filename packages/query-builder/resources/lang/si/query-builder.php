<?php

return [

    'label' => 'විමසුම් තනන්නා',

    'form' => [

        'operator' => [
            'label' => 'ක්‍රියාකාරකය',
        ],

        'or_groups' => [

            'label' => 'කණ්ඩායම්',

            'group' => [
                'label' => 'කණ්ඩායම',
            ],

            'block' => [
                'label' => 'හෝ කොන්දේසිය',
                'picker_group' => 'තර්කනය',
                'or' => 'හෝ',
            ],

        ],

        'rules' => [

            'label' => 'නීති',

            'item' => [
                'and' => 'සහ',
            ],

        ],

    ],

    'no_rules' => '(නීති නැත)',

    'max_rules_reached_tooltip' => 'ඔබ උපරිම නීති :count ට ළඟා වී ඇත.',

    'item_separators' => [
        'and' => 'සහ',
        'or' => 'හෝ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'පුරවා ඇත',
                'inverse' => 'හිස්ය',
            ],

            'summary' => [
                'direct' => ':attribute පුරවා ඇත',
                'inverse' => ':attribute හිස්ය',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'සත්‍යය',
                    'inverse' => 'අසත්‍යය',
                ],

                'summary' => [
                    'direct' => ':attribute සත්‍යය',
                    'inverse' => ':attribute අසත්‍යය',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'පසුව ය',
                    'inverse' => 'පසුව නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :date ට පසුව ය',
                    'inverse' => ':attribute :date ට පසුව නොවේ',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'පෙර ය',
                    'inverse' => 'පෙර නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :date ට පෙර ය',
                    'inverse' => ':attribute :date ට පෙර නොවේ',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'දිනය වේ',
                    'inverse' => 'දිනය නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :date වේ',
                    'inverse' => ':attribute :date නොවේ',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'මාසය වේ',
                    'inverse' => 'මාසය නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :month වේ',
                    'inverse' => ':attribute :month නොවේ',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'වර්ෂය වේ',
                    'inverse' => 'වර්ෂය නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :year වේ',
                    'inverse' => ':attribute :year නොවේ',
                ],

            ],

            'unit_labels' => [
                'second' => 'තත්පර',
                'minute' => 'මිනිත්තු',
                'hour' => 'පැය',
                'day' => 'දින',
                'week' => 'සති',
                'month' => 'මාස',
                'quarter' => 'කාර්තු',
                'year' => 'වසර',
            ],

            'presets' => [
                'past_decade' => 'පසුගිය දශකය',
                'past_5_years' => 'පසුගිය වසර 5',
                'past_2_years' => 'පසුගිය වසර 2',
                'past_year' => 'පසුගිය වසර',
                'past_6_months' => 'පසුගිය මාස 6',
                'past_quarter' => 'පසුගිය කාර්තුව',
                'past_month' => 'පසුගිය මාසය',
                'past_2_weeks' => 'පසුගිය සති 2',
                'past_week' => 'පසුගිය සතිය',
                'past_hour' => 'පසුගිය පැය',
                'past_minute' => 'පසුගිය මිනිත්තුව',
                'this_decade' => 'මෙම දශකය',
                'this_year' => 'මෙම වසර',
                'this_quarter' => 'මෙම කාර්තුව',
                'this_month' => 'මෙම මාසය',
                'today' => 'අද',
                'this_hour' => 'මෙම පැය',
                'this_minute' => 'මෙම මිනිත්තුව',
                'next_minute' => 'ඊළඟ මිනිත්තුව',
                'next_hour' => 'ඊළඟ පැය',
                'next_week' => 'ඊළඟ සතිය',
                'next_2_weeks' => 'ඊළඟ සති 2',
                'next_month' => 'ඊළඟ මාසය',
                'next_quarter' => 'ඊළඟ කාර්තුව',
                'next_6_months' => 'ඊළඟ මාස 6',
                'next_year' => 'ඊළඟ වසර',
                'next_2_years' => 'ඊළඟ වසර 2',
                'next_5_years' => 'ඊළඟ වසර 5',
                'next_decade' => 'ඊළඟ දශකය',
                'custom' => 'අභිරුචි',
            ],

            'form' => [

                'date' => [
                    'label' => 'දිනය',
                ],

                'month' => [
                    'label' => 'මාසය',
                ],

                'year' => [
                    'label' => 'වසර',
                ],

                'mode' => [

                    'label' => 'දින වර්ගය',

                    'options' => [
                        'absolute' => 'නිශ්චිත දිනය',
                        'relative' => 'චලනය වන කාල පරාසය',
                    ],

                ],

                'preset' => [
                    'label' => 'කාල පරිච්ඡේදය',
                ],

                'relative_value' => [
                    'label' => 'කීයක්ද',
                ],

                'relative_unit' => [
                    'label' => 'කාල ඒකකය',
                ],

                'tense' => [

                    'label' => 'කාලය',

                    'options' => [
                        'past' => 'අතීතය',
                        'future' => 'අනාගතය',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'සමාන වේ',
                    'inverse' => 'සමාන නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :number ට සමාන වේ',
                    'inverse' => ':attribute :number ට සමාන නොවේ',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'උපරිමය වේ',
                    'inverse' => 'ට වඩා වැඩි ය',
                ],

                'summary' => [
                    'direct' => ':attribute උපරිම :number වේ',
                    'inverse' => ':attribute :number ට වඩා වැඩි ය',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'අවමය වේ',
                    'inverse' => 'ට වඩා අඩු ය',
                ],

                'summary' => [
                    'direct' => ':attribute අවම :number වේ',
                    'inverse' => ':attribute :number ට වඩා අඩු ය',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'සාමාන්‍යය',
                    'summary' => ':attribute සාමාන්‍යය',
                ],

                'max' => [
                    'label' => 'උපරිමය',
                    'summary' => ':attribute උපරිමය',
                ],

                'min' => [
                    'label' => 'අවමය',
                    'summary' => ':attribute අවමය',
                ],

                'sum' => [
                    'label' => 'එකතුව',
                    'summary' => ':attribute එකතුව',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'සමස්ත ගණනය',
                ],

                'number' => [
                    'label' => 'සංඛ්‍යාව',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'ඇත',
                    'inverse' => 'නැත',
                ],

                'summary' => [
                    'direct' => ':relationship :count ක් ඇත',
                    'inverse' => ':relationship :count ක් නැත',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'උපරිමය ඇත',
                    'inverse' => 'ට වඩා වැඩි ඇත',
                ],

                'summary' => [
                    'direct' => 'උපරිම :relationship :count ක් ඇත',
                    'inverse' => ':relationship :count ට වඩා වැඩි ඇත',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'අවමය ඇත',
                    'inverse' => 'ට වඩා අඩු ඇත',
                ],

                'summary' => [
                    'direct' => 'අවම :relationship :count ක් ඇත',
                    'inverse' => ':relationship :count ට වඩා අඩු ඇත',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'හිස්ය',
                    'inverse' => 'හිස් නොවේ',
                ],

                'summary' => [
                    'direct' => ':relationship හිස්ය',
                    'inverse' => ':relationship හිස් නොවේ',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'වේ',
                        'inverse' => 'නොවේ',
                    ],

                    'multiple' => [
                        'direct' => 'අඩංගු වේ',
                        'inverse' => 'අඩංගු නොවේ',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values වේ',
                        'inverse' => ':relationship :values නොවේ',
                    ],

                    'multiple' => [
                        'direct' => ':relationship හි :values අඩංගු වේ',
                        'inverse' => ':relationship හි :values අඩංගු නොවේ',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' හෝ ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'අගය',
                    ],

                    'values' => [
                        'label' => 'අගයන්',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'ගණන',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'වේ',
                    'inverse' => 'නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :values වේ',
                    'inverse' => ':attribute :values නොවේ',
                    'values_glue' => [
                        ', ',
                        'final' => ' හෝ ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'අගය',
                    ],

                    'values' => [
                        'label' => 'අගයන්',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'අඩංගු වේ',
                    'inverse' => 'අඩංගු නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute හි :text අඩංගු වේ',
                    'inverse' => ':attribute හි :text අඩංගු නොවේ',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'අවසන් වන්නේ',
                    'inverse' => 'අවසන් නොවන්නේ',
                ],

                'summary' => [
                    'direct' => ':attribute අවසන් වන්නේ :text වලින්',
                    'inverse' => ':attribute අවසන් නොවන්නේ :text වලින්',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'සමාන වේ',
                    'inverse' => 'සමාන නොවේ',
                ],

                'summary' => [
                    'direct' => ':attribute :text ට සමාන වේ',
                    'inverse' => ':attribute :text ට සමාන නොවේ',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'ආරම්භ වන්නේ',
                    'inverse' => 'ආරම්භ නොවන්නේ',
                ],

                'summary' => [
                    'direct' => ':attribute ආරම්භ වන්නේ :text වලින්',
                    'inverse' => ':attribute ආරම්භ නොවන්නේ :text වලින්',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'පෙළ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'නීතියක් එක් කරන්න',
        ],

        'add_rule_group' => [
            'label' => 'හෝ එක් කරන්න',
        ],

    ],

];
