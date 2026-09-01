<?php

return [

    'label' => 'So\'rov yaratuvchi',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Guruhlar',

            'block' => [
                'label' => 'Ajralish (YOKI)',
                'or' => 'yoki',
            ],

        ],

        'rules' => [

            'label' => 'Qoidalar',

            'item' => [
                'and' => 'VA',
            ],

        ],

    ],

    'no_rules' => '(Qoidalar yo\'q)',

    'item_separators' => [
        'and' => 'VA',
        'or' => 'YOKI',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'To\'ldirilgan bo\'lsa',
                'inverse' => 'Bo\'sh bo\'lsa',
            ],

            'summary' => [
                'direct' => ':attribute to\'lgan bo\'lsa',
                'inverse' => ':attribute bo\'sh bo\'lsa',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Rost bo\'lsa',
                    'inverse' => 'Yolg\'on bo\'lsa',
                ],

                'summary' => [
                    'direct' => ':attribute rost bo\'lsa',
                    'inverse' => ':attribute yolg\'on bo\'lsa',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Keyin',
                    'inverse' => 'Keyin emas',
                ],

                'summary' => [
                    'direct' => ':date dan keyin :attribute bo\'lsa',
                    'inverse' => ':date :attribute dan keyin bo\'lmasa',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Oldin bo\'lsa',
                    'inverse' => 'Oldin bo\'lmasa',
                ],

                'summary' => [
                    'direct' => ':date dan oldin :attribute bo\'lsa',
                    'inverse' => ':date :attribute dan oldin bo\'lmasa',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Sana bo\'lsa',
                    'inverse' => 'Sana bo\'lmasa',
                ],

                'summary' => [
                    'direct' => ':attribute :date bo\'lsa',
                    'inverse' => ':attribute :date bo\'lmasa',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Oy bo\'lsa',
                    'inverse' => 'Oy bo\'lmasa',
                ],

                'summary' => [
                    'direct' => ':attribute :month bo\'lsa',
                    'inverse' => ':attribute :month bo\'lmasa',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Yil bo\'lsa',
                    'inverse' => 'Yil bo\'lmasa',
                ],

                'summary' => [
                    'direct' => ':attribute :year bo\'lsa',
                    'inverse' => ':attribute :year bo\'lmasa',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Sana',
                ],

                'month' => [
                    'label' => 'Oy',
                ],

                'year' => [
                    'label' => 'Yil',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Teng',
                    'inverse' => 'Teng emas',
                ],

                'summary' => [
                    'direct' => ':attribute teng :number ga ',
                    'inverse' => ':attribute teng emas :number ga',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maksimal bo\'lsa',
                    'inverse' => 'Dan katta bo\'lsa',
                ],

                'summary' => [
                    'direct' => ':attribute maksimal :number',
                    'inverse' => ':attribute :number dan katta',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimal',
                    'inverse' => 'Dan kichkina',
                ],

                'summary' => [
                    'direct' => ':attribute minimal :number',
                    'inverse' => ':attribute :number dan kichkina',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'O\'rtacha',
                    'summary' => 'O\'rtacha :attribute',
                ],

                'max' => [
                    'label' => 'Maksimal',
                    'summary' => 'Maksimal :attribute',
                ],

                'min' => [
                    'label' => 'Minimal',
                    'summary' => 'Minimal :attribute',
                ],

                'sum' => [
                    'label' => 'Umumiy natija (SUM)',
                    'summary' => 'Umumiy natija (SUM) :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregat',
                ],

                'number' => [
                    'label' => 'Raqam',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Mavjud',
                    'inverse' => 'Mavjud emas',
                ],

                'summary' => [
                    'direct' => ':relationship mavjud :count ta',
                    'inverse' => ':relationship mavjud emas :count',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Maksimalga ega',
                    'inverse' => 'Dan ortiq',
                ],

                'summary' => [
                    'direct' => ':relationship :count maksimalga ega',
                    'inverse' => ':relationship :count dan ortiq',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Minimalga ega',
                    'inverse' => 'Dan kam',
                ],

                'summary' => [
                    'direct' => ':relationship :count minimalga ega',
                    'inverse' => ':count :relationship dan ortiq',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Bo\'sh bo\'lsa',
                    'inverse' => 'Bo\'sh bo\'lmasa',
                ],

                'summary' => [
                    'direct' => ':relationship bo\'sh',
                    'inverse' => ':relationship bo\'sh emas',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Hisoblanadi',
                        'inverse' => 'Hisoblanmaydi',
                    ],

                    'multiple' => [
                        'direct' => 'O\'z ichiga oladi',
                        'inverse' => 'O\'z ichiga olmaydi',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':values :relationship',
                        'inverse' => ':values :relationship emas',
                    ],

                    'multiple' => [
                        'direct' => ':relationship :values ni o\'z ichiga oladi',
                        'inverse' => ':relationship :values ni o\'z ichiga olmaydi',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' yoki ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Qiymat',
                    ],

                    'values' => [
                        'label' => 'Qiymatlar',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Soni',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Hisoblanadi',
                    'inverse' => 'Hisoblanmaydi',
                ],

                'summary' => [
                    'direct' => ':attribute :values hisoblanadi',
                    'inverse' => ':attribute :values hisoblanmaydi',
                    'values_glue' => [
                        ', ',
                        'final' => ' yoki ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Qiymat',
                    ],

                    'values' => [
                        'label' => 'Qiymatlar',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'O\'z ichiga oladi',
                    'inverse' => 'O\'z ichiga olmagan',
                ],

                'summary' => [
                    'direct' => ':attribute :text ni o\'z ichiga oladi',
                    'inverse' => ':attribute :text o\'z ichiga olmaydi',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Bilan tugaydi',
                    'inverse' => 'Bilan tugamaydi',
                ],

                'summary' => [
                    'direct' => ':attribute :text bilan tugaydi',
                    'inverse' => ':attribute :text bilan tugamaydi',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Teng',
                    'inverse' => 'Teng emas',
                ],

                'summary' => [
                    'direct' => ':attribute :text ga teng',
                    'inverse' => ':attribute :text ga teng emas',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Bilan boshlanadi',
                    'inverse' => 'Bilan boshlanmaydi',
                ],

                'summary' => [
                    'direct' => ':attribute :text bilan boshlanadi',
                    'inverse' => ':attribute :text bilan boshlanmaydi',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Matn',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Qoida qo\'shish',
        ],

        'add_rule_group' => [
            'label' => 'Qoida guruhini qo\'shish',
        ],

    ],

];
