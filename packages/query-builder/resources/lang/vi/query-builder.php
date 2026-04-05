<?php

return [

    'label' => 'Trình xây dựng truy vấn',

    'form' => [

        'operator' => [
            'label' => 'Toán tử',
        ],

        'or_groups' => [

            'label' => 'Nhóm',

            'block' => [
                'label' => 'Hoặc (OR)',
                'or' => 'HOẶC',
            ],

        ],

        'rules' => [

            'label' => 'Quy tắc',

            'item' => [
                'and' => 'VÀ',
            ],

        ],

    ],

    'no_rules' => '(Không có quy tắc)',

    'item_separators' => [
        'and' => 'VÀ',
        'or' => 'HOẶC',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Giá trị khác rỗng',
                'inverse' => 'Giá trị rỗng',
            ],

            'summary' => [
                'direct' => ':attribute có giá trị khác rỗng',
                'inverse' => ':attribute có giá trị rỗng',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Đúng',
                    'inverse' => 'Sai',
                ],

                'summary' => [
                    'direct' => ':attribute đúng',
                    'inverse' => ':attribute sai',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Sau ngày',
                    'inverse' => 'Không sau ngày',
                ],

                'summary' => [
                    'direct' => ':attribute sau ngày :date',
                    'inverse' => ':attribute không sau ngày :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Trước ngày',
                    'inverse' => 'Không trước ngày',
                ],

                'summary' => [
                    'direct' => ':attribute trước ngày :date',
                    'inverse' => ':attribute không trước ngày :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Là ngày',
                    'inverse' => 'Không là ngày',
                ],

                'summary' => [
                    'direct' => ':attribute là ngày :date',
                    'inverse' => ':attribute không là ngày :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Là tháng',
                    'inverse' => 'Không là tháng',
                ],

                'summary' => [
                    'direct' => ':attribute là tháng :month',
                    'inverse' => ':attribute không là tháng :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Là năm',
                    'inverse' => 'Không là năm',
                ],

                'summary' => [
                    'direct' => ':attribute là năm :year',
                    'inverse' => ':attribute không là năm :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Giây',
                'minute' => 'Phút',
                'hour' => 'Giờ',
                'day' => 'Ngày',
                'week' => 'Tuần',
                'month' => 'Tháng',
                'quarter' => 'Quý',
                'year' => 'Năm',
            ],

            'presets' => [
                'past_decade' => 'Thập kỷ trước',
                'past_5_years' => '5 năm qua',
                'past_2_years' => '2 năm qua',
                'past_year' => 'Năm qua',
                'past_6_months' => '6 tháng qua',
                'past_quarter' => 'Quý trước',
                'past_month' => 'Tháng trước',
                'past_2_weeks' => '2 tuần trước',
                'past_week' => 'Tuần trước',
                'past_hour' => 'Giờ qua',
                'past_minute' => 'Phút qua',
                'this_decade' => 'Thập kỷ này',
                'this_year' => 'Năm nay',
                'this_quarter' => 'Quý này',
                'this_month' => 'Tháng này',
                'today' => 'Hôm nay',
                'this_hour' => 'Giờ này',
                'this_minute' => 'Phút này',
                'next_minute' => 'Phút tới',
                'next_hour' => 'Giờ tới',
                'next_week' => 'Tuần tới',
                'next_2_weeks' => '2 tuần tới',
                'next_month' => 'Tháng tới',
                'next_quarter' => 'Quý tới',
                'next_6_months' => '6 tháng tới',
                'next_year' => 'Năm tới',
                'next_2_years' => '2 năm tới',
                'next_5_years' => '5 năm tới',
                'next_decade' => 'Thập kỷ tới',
                'custom' => 'Tùy chỉnh',
            ],

            'form' => [

                'date' => [
                    'label' => 'Ngày',
                ],

                'month' => [
                    'label' => 'Tháng',
                ],

                'year' => [
                    'label' => 'Năm',
                ],

                'mode' => [

                    'label' => 'Kiểu ngày',

                    'options' => [
                        'absolute' => 'Ngày cụ thể',
                        'relative' => 'Khung thời gian tương đối',
                    ],

                ],

                'preset' => [
                    'label' => 'Khoảng thời gian',
                ],

                'relative_value' => [
                    'label' => 'Bao nhiêu',
                ],

                'relative_unit' => [
                    'label' => 'Đơn vị thời gian',
                ],

                'tense' => [

                    'label' => 'Thì',

                    'options' => [
                        'past' => 'Quá khứ',
                        'future' => 'Tương lai',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Bằng',
                    'inverse' => 'Không bằng',
                ],

                'summary' => [
                    'direct' => ':attribute bằng :number',
                    'inverse' => ':attribute không bằng :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Là tối đa',
                    'inverse' => 'Lớn hơn',
                ],

                'summary' => [
                    'direct' => ':attribute là tối đa :number',
                    'inverse' => ':attribute lớn hơn :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Là tối thiểu',
                    'inverse' => 'Nhỏ hơn',
                ],

                'summary' => [
                    'direct' => ':attribute là tối thiểu :number',
                    'inverse' => ':attribute nhỏ hơn :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Trung bình',
                    'summary' => 'Trung bình :attribute',
                ],

                'max' => [
                    'label' => 'Tối đa',
                    'summary' => 'Tối đa :attribute',
                ],

                'min' => [
                    'label' => 'Tối thiểu',
                    'summary' => 'Tối thiểu :attribute',
                ],

                'sum' => [
                    'label' => 'Tổng',
                    'summary' => 'Tổng của :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Tổng hợp',
                ],

                'number' => [
                    'label' => 'Số',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Có',
                    'inverse' => 'Không có',
                ],

                'summary' => [
                    'direct' => 'Có :count :relationship',
                    'inverse' => 'Không có :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Có tối đa',
                    'inverse' => 'Có nhiều hơn',
                ],

                'summary' => [
                    'direct' => 'Có tối đa :count :relationship',
                    'inverse' => 'Có nhiều hơn :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Có tối thiểu',
                    'inverse' => 'Có ít hơn',
                ],

                'summary' => [
                    'direct' => 'Có tối thiểu :count :relationship',
                    'inverse' => 'Có ít hơn :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Trống',
                    'inverse' => 'Không trống',
                ],

                'summary' => [
                    'direct' => ':relationship trống',
                    'inverse' => ':relationship không trống',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Là',
                        'inverse' => 'Không là',
                    ],

                    'multiple' => [
                        'direct' => 'Chứa',
                        'inverse' => 'Không chứa',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship là :values',
                        'inverse' => ':relationship không là :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship chứa :values',
                        'inverse' => ':relationship không chứa :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' hoặc ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Giá trị',
                    ],

                    'values' => [
                        'label' => 'Các giá trị',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Số lượng',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Là',
                    'inverse' => 'Không phải là',
                ],

                'summary' => [
                    'direct' => ':attribute là :values',
                    'inverse' => ':attribute không phải là :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' hoặc ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Giá trị',
                    ],

                    'values' => [
                        'label' => 'Các giá trị',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Chứa',
                    'inverse' => 'Không chứa',
                ],

                'summary' => [
                    'direct' => ':attribute chứa :text',
                    'inverse' => ':attribute không chứa :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Kết thúc bằng',
                    'inverse' => 'Không kết thúc bằng',
                ],

                'summary' => [
                    'direct' => ':attribute kết thúc bằng :text',
                    'inverse' => ':attribute không kết thúc bằng :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Bằng',
                    'inverse' => 'Không bằng',
                ],

                'summary' => [
                    'direct' => ':attribute bằng :text',
                    'inverse' => ':attribute không bằng :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Bắt đầu bằng',
                    'inverse' => 'Không bắt đầu bằng',
                ],

                'summary' => [
                    'direct' => ':attribute bắt đầu bằng :text',
                    'inverse' => ':attribute không bắt đầu bằng :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Văn bản',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Thêm quy tắc',
        ],

        'add_rule_group' => [
            'label' => 'Thêm nhóm quy tắc',
        ],

    ],

];
