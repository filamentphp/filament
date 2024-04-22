<?php

return [

    'label' => 'สร้างคำค้น',

    'form' => [

        'operator' => [
            'label' => 'ดำเนินการ',
        ],

        'or_groups' => [

            'label' => 'กลุ่ม',

            'block' => [
                'label' => 'แยกเงื่อนไข (หรือ)',
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
                    'direct' => ':attribute is :month',
                    'inverse' => ':attribute is not :month',
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
                        'final' => ' or ',
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
