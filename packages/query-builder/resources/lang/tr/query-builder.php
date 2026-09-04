<?php

return [

    'label' => 'Sorgu oluşturucu',

    'form' => [

        'operator' => [
            'label' => 'Operatör',
        ],

        'or_groups' => [

            'label' => 'Gruplar',

            'block' => [
                'label' => 'Veya (OR)',
                'or' => 'VEYA',
            ],

            'group' => [
                'label' => 'Grup',
            ],

        ],

        'rules' => [

            'label' => 'Kurallar',

            'item' => [
                'and' => 'VE',
            ],

        ],

    ],

    'no_rules' => '(Kural yok)',

    'max_rules_reached_tooltip' => 'Maksimum :count kural sınırına ulaştınız.',

    'item_separators' => [
        'and' => 'VE',
        'or' => 'VEYA',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Dolu',
                'inverse' => 'Boş',
            ],

            'summary' => [
                'direct' => ':attribute dolu',
                'inverse' => ':attribute boş',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Doğru',
                    'inverse' => 'Yanlış',
                ],

                'summary' => [
                    'direct' => ':attribute doğru',
                    'inverse' => ':attribute yanlış',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Sonra',
                    'inverse' => 'Sonra değil',
                ],

                'summary' => [
                    'direct' => ':attribute :date tarihinden sonra',
                    'inverse' => ':attribute :date tarihinden sonra değil',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Önce',
                    'inverse' => 'Önce değil',
                ],

                'summary' => [
                    'direct' => ':attribute :date tarihinden önce',
                    'inverse' => ':attribute :date tarihinden önce değil',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Tarihtir',
                    'inverse' => 'Tarih değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :date',
                    'inverse' => ':attribute :date değil',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Aydır',
                    'inverse' => 'Ay değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :month',
                    'inverse' => ':attribute :month değil',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Yıldır',
                    'inverse' => 'Yıl değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :year',
                    'inverse' => ':attribute :year değil',
                ],

            ],

            'unit_labels' => [
                'second' => 'Saniye',
                'minute' => 'Dakika',
                'hour' => 'Saat',
                'day' => 'Gün',
                'week' => 'Hafta',
                'month' => 'Ay',
                'quarter' => 'Çeyrek',
                'year' => 'Yıl',
            ],

            'presets' => [
                'past_decade' => 'Geçtiğimiz 10 yıl',
                'past_5_years' => 'Geçtiğimiz 5 yıl',
                'past_2_years' => 'Geçtiğimiz 2 yıl',
                'past_year' => 'Geçtiğimiz yıl',
                'past_6_months' => 'Geçtiğimiz 6 ay',
                'past_quarter' => 'Geçtiğimiz çeyrek',
                'past_month' => 'Geçtiğimiz ay',
                'past_2_weeks' => 'Geçtiğimiz 2 hafta',
                'past_week' => 'Geçtiğimiz hafta',
                'past_hour' => 'Geçtiğimiz saat',
                'past_minute' => 'Geçtiğimiz dakika',
                'this_decade' => 'Bu 10 yıl',
                'this_year' => 'Bu yıl',
                'this_quarter' => 'Bu çeyrek',
                'this_month' => 'Bu ay',
                'today' => 'Bugün',
                'this_hour' => 'Bu saat',
                'this_minute' => 'Bu dakika',
                'next_minute' => 'Gelecek dakika',
                'next_hour' => 'Gelecek saat',
                'next_week' => 'Gelecek hafta',
                'next_2_weeks' => 'Gelecek 2 hafta',
                'next_month' => 'Gelecek ay',
                'next_quarter' => 'Gelecek çeyrek',
                'next_6_months' => 'Gelecek 6 ay',
                'next_year' => 'Gelecek yıl',
                'next_2_years' => 'Gelecek 2 yıl',
                'next_5_years' => 'Gelecek 5 yıl',
                'next_decade' => 'Gelecek 10 yıl',
                'custom' => 'Özel',
            ],

            'form' => [

                'date' => [
                    'label' => 'Tarih',
                ],

                'month' => [
                    'label' => 'Ay',
                ],

                'year' => [
                    'label' => 'Yıl',
                ],

                'mode' => [
                    'label' => 'Tarih türü',
                    'options' => [
                        'absolute' => 'Belirli tarih',
                        'relative' => 'Dinamik zaman aralığı',
                    ],
                ],

                'preset' => [
                    'label' => 'Zaman aralığı',
                ],

                'relative_value' => [
                    'label' => 'Miktar',
                ],

                'relative_unit' => [
                    'label' => 'Zaman birimi',
                ],

                'tense' => [
                    'label' => 'Zaman yönü',
                    'options' => [
                        'past' => 'Geçmiş',
                        'future' => 'Gelecek',
                    ],
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Eşittir',
                    'inverse' => 'Eşit değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :number\'a eşittir',
                    'inverse' => ':attribute :number\'a eşit değildir',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maksimum',
                    'inverse' => 'Büyüktür',
                ],

                'summary' => [
                    'direct' => ':attribute maksimum :number',
                    'inverse' => ':attribute :number\'dan büyük',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimum',
                    'inverse' => 'Küçüktür',
                ],

                'summary' => [
                    'direct' => ':attribute minimum :number',
                    'inverse' => ':attribute :number\'dan küçük',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Ortalama',
                    'summary' => 'Ortalama :attribute',
                ],

                'max' => [
                    'label' => 'Maksimum',
                    'summary' => 'Maksimum :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Toplam',
                    'summary' => ':attribute toplamı',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Toplam',
                ],

                'number' => [
                    'label' => 'Sayı',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Sahip',
                    'inverse' => 'Sahip değil',
                ],

                'summary' => [
                    'direct' => ':count :relationship mevcut',
                    'inverse' => ':count :relationship mevcut değil',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'En fazla',
                    'inverse' => 'Daha fazla',
                ],

                'summary' => [
                    'direct' => 'En fazla :count :relationship',
                    'inverse' => ':count adetten fazla :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'En az',
                    'inverse' => 'Daha az',
                ],

                'summary' => [
                    'direct' => 'En az :count :relationship',
                    'inverse' => ':count adetten az :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Boş',
                    'inverse' => 'Boş değil',
                ],

                'summary' => [
                    'direct' => ':relationship boş',
                    'inverse' => ':relationship boş değil',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Eşittir',
                        'inverse' => 'Eşit değildir',
                    ],

                    'multiple' => [
                        'direct' => 'İçerir',
                        'inverse' => 'İçermez',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values',
                        'inverse' => ':relationship :values değil',
                    ],

                    'multiple' => [
                        'direct' => ':relationship :values içerir',
                        'inverse' => ':relationship :values içermez',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' veya ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Değer',
                    ],

                    'values' => [
                        'label' => 'Değerler',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Sayı',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Eşittir',
                    'inverse' => 'Eşit değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :values',
                    'inverse' => ':attribute :values değil',
                    'values_glue' => [
                        ', ',
                        'final' => ' veya ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Değer',
                    ],

                    'values' => [
                        'label' => 'Değerler',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'İçerir',
                    'inverse' => 'İçermez',
                ],

                'summary' => [
                    'direct' => ':attribute :text içerir',
                    'inverse' => ':attribute :text içermez',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Şununla biter',
                    'inverse' => 'Şununla bitmez',
                ],

                'summary' => [
                    'direct' => ':attribute :text ile biter',
                    'inverse' => ':attribute :text ile bitmez',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Eşittir',
                    'inverse' => 'Eşit değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :text\'e eşittir',
                    'inverse' => ':attribute :text\'e eşit değildir',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Şununla başlar',
                    'inverse' => 'Şununla başlamaz',
                ],

                'summary' => [
                    'direct' => ':attribute :text ile başlar',
                    'inverse' => ':attribute :text ile başlamaz',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Metin',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Kural ekle',
        ],

        'add_rule_group' => [
            'label' => 'Kural grubu ekle',
        ],

    ],

];
