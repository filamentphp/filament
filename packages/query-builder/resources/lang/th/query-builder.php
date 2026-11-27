<?php

return [

    'label' => 'สร้างคำค้น',

    'form' => [

        'operator' => [
            'label' => 'ดำเนินการ',
        ],

        'or_groups' => [

            'label' => 'กลุ่ม',

            'group' => [
                'label' => 'กลุ่ม',
            ],

            'block' => [
                'label' => 'แยกเงื่อนไข (หรือ)',
                'picker_group' => 'ตรรกะ',
                'or' => 'หรือ',
            ],

        ],

        'rules' => [

            'label' => 'เงื่อนไข',

            'item' => [
                'and' => 'และ',
            ],

        ],

    ],

    'no_rules' => '(ไม่มีเงื่อนไข)',

    'max_rules_reached_tooltip' => 'คุณเพิ่มเงื่อนไขครบจำนวนสูงสุด :count เงื่อนไขแล้ว',

    'item_separators' => [
        'and' => 'และ',
        'or' => 'หรือ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'มีข้อมูล',
                'inverse' => 'ว่าง',
            ],

            'summary' => [
                'direct' => ':attribute มีข้อมูล',
                'inverse' => ':attribute ว่าง',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'เป็นจริง',
                    'inverse' => 'เป็นเท็จ',
                ],

                'summary' => [
                    'direct' => ':attribute เป็นจริง',
                    'inverse' => ':attribute เป็นเท็จ',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'หลังจาก',
                    'inverse' => 'ไม่เกิน',
                ],

                'summary' => [
                    'direct' => ':attribute หลังจาก :date',
                    'inverse' => ':attribute ไม่เกิน :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'ก่อน',
                    'inverse' => 'ตั้งแต่',
                ],

                'summary' => [
                    'direct' => ':attribute ก่อน :date',
                    'inverse' => ':attribute ตั้งแต่ :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'วันที่',
                    'inverse' => 'ไม่ใช่วันที่',
                ],

                'summary' => [
                    'direct' => ':attribute เป็น :date',
                    'inverse' => ':attribute ไม่ใช่ :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'เดือน',
                    'inverse' => 'ไม่ใช่เดือน',
                ],

                'summary' => [
                    'direct' => ':attribute เป็นเดือน :month',
                    'inverse' => ':attribute ไม่ใช่เดือน :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'ปี',
                    'inverse' => 'ไม่ใช่ปี',
                ],

                'summary' => [
                    'direct' => ':attribute ปี :year',
                    'inverse' => ':attribute ไม่ใช่ปี :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'วินาที',
                'minute' => 'นาที',
                'hour' => 'ชั่วโมง',
                'day' => 'วัน',
                'week' => 'สัปดาห์',
                'month' => 'เดือน',
                'quarter' => 'ไตรมาส',
                'year' => 'ปี',
            ],

            'presets' => [
                'past_decade' => '10 ปีที่แล้ว',
                'past_5_years' => '5 ปีที่แล้ว',
                'past_2_years' => '2 ปีที่แล้ว',
                'past_year' => '1 ปีที่แล้ว',
                'past_6_months' => '6 เดือนที่แล้ว',
                'past_quarter' => '1 ไตรมาสที่แล้ว',
                'past_month' => '1 เดือนที่แล้ว',
                'past_2_weeks' => '2 สัปดาห์ที่แล้ว',
                'past_week' => '1 สัปดาห์ที่แล้ว',
                'past_hour' => '1 ชั่วโมงที่แล้ว',
                'past_minute' => '1 นาทีที่แล้ว',
                'this_decade' => 'ทศวรรษนี้',
                'this_year' => 'ปีนี้',
                'this_quarter' => 'ไตรมาสนี้',
                'this_month' => 'เดือนนี้',
                'today' => 'วันนี้',
                'this_hour' => 'ชั่วโมงนี้',
                'this_minute' => 'นาทีนี้',
                'next_minute' => '1 นาทีข้างหน้า',
                'next_hour' => '1 ชั่วโมงข้างหน้า',
                'next_week' => '1 สัปดาห์ข้างหน้า',
                'next_2_weeks' => '2 สัปดาห์ข้างหน้า',
                'next_month' => '1 เดือนข้างหน้า',
                'next_quarter' => '1 ไตรมาสข้างหน้า',
                'next_6_months' => '6 เดือนข้างหน้า',
                'next_year' => '1 ปีข้างหน้า',
                'next_2_years' => '2 ปีข้างหน้า',
                'next_5_years' => '5 ปีข้างหน้า',
                'next_decade' => '10 ปีข้างหน้า',
                'custom' => 'กำหนดเอง',
            ],

            'form' => [

                'date' => [
                    'label' => 'วัน',
                ],

                'month' => [
                    'label' => 'เดือน',
                ],

                'year' => [
                    'label' => 'ปี',
                ],

                'mode' => [

                    'label' => 'ประเภทวันที่',

                    'options' => [
                        'absolute' => 'ระบุวันที่',
                        'relative' => 'นับจากปัจจุบัน',
                    ],

                ],

                'preset' => [
                    'label' => 'ช่วงเวลา',
                ],

                'relative_value' => [
                    'label' => 'จำนวน',
                ],

                'relative_unit' => [
                    'label' => 'หน่วยเวลา',
                ],

                'tense' => [

                    'label' => 'ทิศทางเวลา',

                    'options' => [
                        'past' => 'ย้อนหลัง',
                        'future' => 'ล่วงหน้า',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'เท่ากับ',
                    'inverse' => 'ไม่เท่ากับ',
                ],

                'summary' => [
                    'direct' => ':attribute เท่ากับ :number',
                    'inverse' => ':attribute ไม่เท่ากับ :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'ไม่เกิน',
                    'inverse' => 'มากกว่า',
                ],

                'summary' => [
                    'direct' => ':attribute ไม่เกิน :number',
                    'inverse' => ':attribute มากกว่า :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'อย่างน้อย',
                    'inverse' => 'น้อยกว่า',
                ],

                'summary' => [
                    'direct' => ':attribute อย่างน้อย :number',
                    'inverse' => ':attribute น้อยกว่า :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'ค่าเฉลี่ย',
                    'summary' => ':attribute เฉลี่ย',
                ],

                'max' => [
                    'label' => 'สูงสุด',
                    'summary' => ':attribute สูงสุด',
                ],

                'min' => [
                    'label' => 'ต่ำสุด',
                    'summary' => ':attribute ต่ำสุด',
                ],

                'sum' => [
                    'label' => 'รวม',
                    'summary' => ':attribute รวม',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'ผลรวม',
                ],

                'number' => [
                    'label' => 'ตัวเลข',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'มี',
                    'inverse' => 'ไม่มี',
                ],

                'summary' => [
                    'direct' => 'มี :count :relationship',
                    'inverse' => 'ไม่มี :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'มีสูงสุด',
                    'inverse' => 'มีมากกว่า',
                ],

                'summary' => [
                    'direct' => 'มีสูงสุด :count :relationship',
                    'inverse' => 'มีมากกว่า :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'มีขั้นต่ำ',
                    'inverse' => 'มีน้อยกว่า',
                ],

                'summary' => [
                    'direct' => 'มีขั้นต่ำ :count :relationship',
                    'inverse' => 'มีน้อยกว่า :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ว่าง',
                    'inverse' => 'ไม่ว่าง',
                ],

                'summary' => [
                    'direct' => ':relationship ว่าง',
                    'inverse' => ':relationship ไม่ว่าง',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'เกี่ยวกับ',
                        'inverse' => 'ไม่เกี่ยวกับ',
                    ],

                    'multiple' => [
                        'direct' => 'มี',
                        'inverse' => 'ไม่มี',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship คือ :values',
                        'inverse' => ':relationship ไม่ใช่ :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship มี :values',
                        'inverse' => ':relationship ไม่มี :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' หรือ ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'ค่า',
                    ],

                    'values' => [
                        'label' => 'ค่า',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'จำนวน',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'คือ',
                    'inverse' => 'ไม่ใช่',
                ],

                'summary' => [
                    'direct' => ':attribute :values',
                    'inverse' => ':attribute ไม่ใช่ :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' หรือ ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'ค่า',
                    ],

                    'values' => [
                        'label' => 'ค่า',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'มีคำว่า',
                    'inverse' => 'ไม่มีคำว่า',
                ],

                'summary' => [
                    'direct' => ':attribute มีคำว่า :text',
                    'inverse' => ':attribute ไม่มีคำว่า :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'ลงท้ายด้วย',
                    'inverse' => 'ไม่ลงท้ายด้วย',
                ],

                'summary' => [
                    'direct' => ':attribute ลงท้ายด้วย :text',
                    'inverse' => ':attribute ไม่ลงท้ายด้วย :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'เท่ากับ',
                    'inverse' => 'ไม่เท่ากับ',
                ],

                'summary' => [
                    'direct' => ':attribute เท่ากับ :text',
                    'inverse' => ':attribute ไม่เท่ากับ :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'ขึ้นต้นด้วย',
                    'inverse' => 'ไม่ขึ้นต้นด้วย',
                ],

                'summary' => [
                    'direct' => ':attribute ขึ้นต้นด้วย :text',
                    'inverse' => ':attribute ไม่ขึ้นต้นด้วย :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'ข้อความ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'เพิ่มเงื่อนไข',
        ],

        'add_rule_group' => [
            'label' => 'เพิ่มกลุ่มเงื่อนไข',
        ],

    ],

];
