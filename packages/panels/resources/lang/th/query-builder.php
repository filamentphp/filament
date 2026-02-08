<?php

return [

    'label' => 'ตัวสร้างคำสั่งคิวรี',

    'form' => [
        'operator' => [
            'label' => 'ตัวดำเนินการ',
        ],
        'rule' => [
            'label' => 'กฎ',
        ],
        'value' => [
            'label' => 'ค่า',
        ],
    ],

    'operators' => [

        'is_filled' => [
            'label' => 'มีค่า',
        ],

        'is_blank' => [
            'label' => 'เป็นค่าว่าง',
        ],

        'boolean' => [

            'is_true' => [
                'label' => 'เป็นจริง',
            ],

            'is_false' => [
                'label' => 'เป็นเท็จ',
            ],

        ],

        'date' => [

            'is_after' => [
                'label' => 'หลังจาก',
            ],

            'is_before' => [
                'label' => 'ก่อนหน้า',
            ],

            'is_year' => [
                'label' => 'เป็นปี',
            ],

        ],

        'number' => [

            'is_equals' => [
                'label' => 'เท่ากับ',
            ],

            'is_not_equals' => [
                'label' => 'ไม่เท่ากับ',
            ],

            'is_greater_than' => [
                'label' => 'มากกว่า',
            ],

            'is_less_than' => [
                'label' => 'น้อยกว่า',
            ],

            'is_greater_than_or_equal_to' => [
                'label' => 'มากกว่าหรือเท่ากับ',
            ],

            'is_less_than_or_equal_to' => [
                'label' => 'น้อยกว่าหรือเท่ากับ',
            ],

        ],

        'select' => [

            'is_equals' => [
                'label' => 'เท่ากับ',
            ],

            'is_not_equals' => [
                'label' => 'ไม่เท่ากับ',
            ],

        ],

        'text' => [

            'contains' => [
                'label' => 'ประกอบด้วย',
            ],

            'does_not_contain' => [
                'label' => 'ไม่ประกอบด้วย',
            ],

            'equals' => [
                'label' => 'เท่ากับ',
            ],

            'does_not_equal' => [
                'label' => 'ไม่เท่ากับ',
            ],

            'starts_with' => [
                'label' => 'เริ่มต้นด้วย',
            ],

            'ends_with' => [
                'label' => 'สิ้นสุดด้วย',
            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'เพิ่มกฎ',
        ],

        'add_rule_group' => [
            'label' => 'เพิ่มกลุ่มกฎ',
        ],

    ],

];
