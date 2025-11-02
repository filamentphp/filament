<?php

return [

    'label' => 'Query бүтээгч',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Бүлгүүд',

            'block' => [
                'label' => 'Disjunction (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Дүрмүүд',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Дүрмүүд алга)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'бөглөгдсөн',
                'inverse' => 'хоосон',
            ],

            'summary' => [
                'direct' => ':attribute бөглөгдсөн',
                'inverse' => ':attribute хоосон',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Үнэн',
                    'inverse' => 'Худал',
                ],

                'summary' => [
                    'direct' => ':attribute бол үнэн',
                    'inverse' => ':attributeбол худал',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Дараа нь',
                    'inverse' => 'Дараа нь биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :date -ний дараа',
                    'inverse' => ':attribute нь :date -ний дараа биш',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'өмнө',
                    'inverse' => 'өмнө нь биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :date - ний өмнө',
                    'inverse' => ':attribute нь :date - ний өмнө биш',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Огноо',
                    'inverse' => 'Огноо биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :date',
                    'inverse' => ':attribute бнь биш :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'сар',
                    'inverse' => 'сар биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :month',
                    'inverse' => ':attribute нь биш :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'жил',
                    'inverse' => 'жил биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :year',
                    'inverse' => ':attribute нь биш :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Огноо',
                ],

                'month' => [
                    'label' => 'Сар',
                ],

                'year' => [
                    'label' => 'Жил',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Тэнцүү',
                    'inverse' => 'Тэнцүү биш',
                ],

                'summary' => [
                    'direct' => ':attribute тэнцүү :number',
                    'inverse' => ':attribute тэнцүү биш :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Хамгийн их',
                    'inverse' => '-ээс их',
                ],

                'summary' => [
                    'direct' => ':attribute нь хамгийн их :number',
                    'inverse' => ':attribute нь :number - ээс их',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Хамгийн бага',
                    'inverse' => '-ээс бага',
                ],

                'summary' => [
                    'direct' => ':attribute нь хамгийн бага :number',
                    'inverse' => ':attribute нь :number - ээс бага',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Дундаж',
                    'summary' => 'Дундаж :attribute',
                ],

                'max' => [
                    'label' => 'Хамгийн их',
                    'summary' => 'Хамгийн их :attribute',
                ],

                'min' => [
                    'label' => 'Хамгийн бага',
                    'summary' => 'Хамгийн бага :attribute',
                ],

                'sum' => [
                    'label' => 'Нийлбэр',
                    'summary' => 'Нийлбэр :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Агрегат',
                ],

                'number' => [
                    'label' => 'Тоо',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Агуулсан',
                    'inverse' => 'Агуулаагүй',
                ],

                'summary' => [
                    'direct' => ':count агуулсан :relationship',
                    'inverse' => ':count агуулаагүй :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Хамгийн их',
                    'inverse' => 'Илүү',
                ],

                'summary' => [
                    'direct' => 'Хамгийн их :count :relationship',
                    'inverse' => 'Ихийг агуулсан :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Хамгийн багыг агуулсан',
                    'inverse' => 'Багыг агуулсан',
                ],

                'summary' => [
                    'direct' => 'Хамгийн бага :count :relationship',
                    'inverse' => 'Багыг агуулсан :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Хоосон',
                    'inverse' => 'Хоосон биш',
                ],

                'summary' => [
                    'direct' => ':relationship хоосон',
                    'inverse' => ':relationship хоосон биш',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Бол',
                        'inverse' => 'бол биш',
                    ],

                    'multiple' => [
                        'direct' => 'Агуулсан',
                        'inverse' => 'Агуулаагүй',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship нь :values',
                        'inverse' => ':relationship нь :values биш',
                    ],

                    'multiple' => [
                        'direct' => ':relationship агуулсан утгууд :values',
                        'inverse' => ':relationship агуулаагүй утгууд :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Утга',
                    ],

                    'values' => [
                        'label' => 'Утгууд',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Тоолох',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'мөн',
                    'inverse' => 'биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :values',
                    'inverse' => ':attribute нь :values биш',
                    'values_glue' => [
                        ', ',
                        'final' => ' or ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Утга',
                    ],

                    'values' => [
                        'label' => 'Утгууд',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Агуулсан',
                    'inverse' => 'Агуулаагүй',
                ],

                'summary' => [
                    'direct' => ':attribute нь :text - ийг агуулсан',
                    'inverse' => ':attribute нь :text - ийг агуулаагүй',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Төгссөн',
                    'inverse' => 'Төгсөөгүй',
                ],

                'summary' => [
                    'direct' => ':attribute нь :text -ээр төгссөн',
                    'inverse' => ':attribute нь :text -ээр төгсөөгүй',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Тэнцүү',
                    'inverse' => 'Тэнцүү биш',
                ],

                'summary' => [
                    'direct' => ':attribute нь :text -тэй тэнцүү',
                    'inverse' => ':attribute нь :text -тэй тэнцүү биш',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Эхлэсэн',
                    'inverse' => 'Эхлээгүй',
                ],

                'summary' => [
                    'direct' => ':attribute нь :text - ээр эхлэсэн',
                    'inverse' => ':attribute нь :text - эээр эхлээгүй',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Тэкст',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Дүрэм нэмэх',
        ],

        'add_rule_group' => [
            'label' => 'Бүлэг дүрэм нэмэх',
        ],

    ],

];
