<?php

return [

    'label' => 'ქუერი ბილდერი',

    'form' => [

        'operator' => [
            'label' => 'ოპერატორი',
        ],

        'or_groups' => [

            'label' => 'ჯგუფები',

            'block' => [
                'label' => 'დისიუნქცია (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'წესები',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(არ არის წესები)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'შევსებულია',
                'inverse' => 'ცარიელია',
            ],

            'summary' => [
                'direct' => ':attribute შევსებულია',
                'inverse' => ':attribute ცარიელია',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'მართალია',
                    'inverse' => 'მცდარია',
                ],

                'summary' => [
                    'direct' => ':attribute მართალია',
                    'inverse' => ':attribute მცდარია',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'შემდეგია',
                    'inverse' => 'არ არის შემდეგი',
                ],

                'summary' => [
                    'direct' => ':attribute შემდეგია :date-ის შემდეგ',
                    'inverse' => ':attribute არ არის შემდეგი :date-ის შემდეგ',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'მანამდეა',
                    'inverse' => 'არ არის მანამდე',
                ],

                'summary' => [
                    'direct' => ':attribute მანამდეა :date-მდე',
                    'inverse' => ':attribute არ არის მანამდე :date-მდე',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'არის თარიღი',
                    'inverse' => 'არ არის თარიღი',
                ],

                'summary' => [
                    'direct' => ':attribute არის :date',
                    'inverse' => ':attribute არ არის :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'არის თვე',
                    'inverse' => 'არ არის თვე',
                ],

                'summary' => [
                    'direct' => ':attribute არის :month',
                    'inverse' => ':attribute არ არის :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'არის წელი',
                    'inverse' => 'არ არის წელი',
                ],

                'summary' => [
                    'direct' => ':attribute არის :year',
                    'inverse' => ':attribute არ არის :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'თარიღი',
                ],

                'month' => [
                    'label' => 'თვე',
                ],

                'year' => [
                    'label' => 'წელი',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'თანაბარია',
                    'inverse' => 'არ არის თანაბარი',
                ],

                'summary' => [
                    'direct' => ':attribute თანაბარია :number',
                    'inverse' => ':attribute არ არის თანაბარი :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'არის მაქსიმუმი',
                    'inverse' => 'მეტია ვიდრე',
                ],

                'summary' => [
                    'direct' => ':attribute არის მაქსიმუმი :number',
                    'inverse' => ':attribute მეტია ვიდრე :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'არის მინიმუმი',
                    'inverse' => 'ნაკლებია ვიდრე',
                ],

                'summary' => [
                    'direct' => ':attribute არის მინიმუმი :number',
                    'inverse' => ':attribute ნაკლებია ვიდრე :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'საშუალო',
                    'summary' => 'საშუალო :attribute',
                ],

                'max' => [
                    'label' => 'მაქსიმუმი',
                    'summary' => 'მაქსიმუმი :attribute',
                ],

                'min' => [
                    'label' => 'მინიმუმი',
                    'summary' => 'მინიმუმი :attribute',
                ],

                'sum' => [
                    'label' => 'ჯამი',
                    'summary' => ':attribute ჯამი',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'აგრეგატი',
                ],

                'number' => [
                    'label' => 'რიცხვი',
                ],

            ],
        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'აქვს',
                    'inverse' => 'არ აქვს',
                ],

                'summary' => [
                    'direct' => 'აქვს :count :relationship',
                    'inverse' => 'არ აქვს :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'აქვს მაქსიმუმი',
                    'inverse' => 'მეტია ვიდრე',
                ],

                'summary' => [
                    'direct' => 'აქვს მაქსიმუმი :count :relationship',
                    'inverse' => 'მეტია ვიდრე :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'აქვს მინიმუმი',
                    'inverse' => 'ნაკლებია ვიდრე',
                ],

                'summary' => [
                    'direct' => 'აქვს მინიმუმი :count :relationship',
                    'inverse' => 'ნაკლებია ვიდრე :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ცარიელია',
                    'inverse' => 'არ არის ცარიელი',
                ],

                'summary' => [
                    'direct' => ':relationship ცარიელია',
                    'inverse' => ':relationship არ არის ცარიელი',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'არის',
                        'inverse' => 'არ არის',
                    ],

                    'multiple' => [
                        'direct' => 'შეიცავს',
                        'inverse' => 'არ შეიცავს',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship არის :values',
                        'inverse' => ':relationship არ არის :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship შეიცავს :values',
                        'inverse' => ':relationship არ შეიცავს :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ან ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'მნიშვნელობა',
                    ],

                    'values' => [
                        'label' => 'მნიშვნელობები',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'რაოდენობა',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'არის',
                    'inverse' => 'არ არის',
                ],

                'summary' => [
                    'direct' => ':attribute არის :values',
                    'inverse' => ':attribute არ არის :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ან ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'მნიშვნელობა',
                    ],

                    'values' => [
                        'label' => 'მნიშვნელობები',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'შეიცავს',
                    'inverse' => 'არ შეიცავს',
                ],

                'summary' => [
                    'direct' => ':attribute შეიცავს :text',
                    'inverse' => ':attribute არ შეიცავს :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'მთავრდება',
                    'inverse' => 'არ მთავრდება',
                ],

                'summary' => [
                    'direct' => ':attribute მთავრდება :text-ზე',
                    'inverse' => ':attribute არ მთავრდება :text-ზე',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'თანაბარია',
                    'inverse' => 'არ არის თანაბარი',
                ],

                'summary' => [
                    'direct' => ':attribute თანაბარია :text',
                    'inverse' => ':attribute არ არის თანაბარი :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'იწყება',
                    'inverse' => 'არ იწყება',
                ],

                'summary' => [
                    'direct' => ':attribute იწყება :text-ით',
                    'inverse' => ':attribute არ იწყება :text-ით',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'ტექსტი',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'დამატება წესის',
        ],

        'add_rule_group' => [
            'label' => 'დამატება წესების ჯგუფის',
        ],

    ],

];
