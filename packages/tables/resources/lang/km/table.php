<?php

return [

    'column_manager' => [

        'heading' => 'ជួរឈរ',

    ],

    'columns' => [

        'actions' => [
            'label' => 'សកម្មភាព|សកម្មភាព',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'បង្ហាញ :count តិច',
                'expand_list' => 'បង្ហាញ :count ច្រើនទៀត',
            ],

            'more_list_items' => 'និង :count ច្រើនទៀត',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'ជ្រើសរើស/មិនជ្រើសរើសធាតុទាំងអស់សម្រាប់សកម្មភាពភាគច្រើន.',
        ],

        'bulk_select_record' => [
            'label' => 'ជ្រើសរើស/មិនជ្រើសរើសធាតុ :key សម្រាប់សកម្មភាពភាគច្រើន.',
        ],

        'bulk_select_group' => [
            'label' => 'ជ្រើសរើស/មិនជ្រើសរើសក្រុម :title សម្រាប់សកម្មភាពភាគច្រើន.',
        ],

        'search' => [
            'label' => 'ស្វែងរក',
            'placeholder' => 'ស្វែងរក',
            'indicator' => 'ស្វែងរក',
        ],

    ],

    'summary' => [

        'heading' => 'សង្ខេប',

        'subheadings' => [
            'all' => 'ទាំងអស់ :label',
            'group' => ':group សង្ខេប',
            'page' => 'ទំព័រនេះ',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'មធ្យម',
            ],

            'count' => [
                'label' => 'រាប់',
            ],

            'sum' => [
                'label' => 'បូក',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'បញ្ចប់​ការ​រៀបចំ​កំណត់ត្រា​ឡើងវិញ',
        ],

        'enable_reordering' => [
            'label' => 'តម្រៀបកំណត់ត្រាឡើងវិញ',
        ],

        'filter' => [
            'label' => 'តម្រង',
        ],

        'group' => [
            'label' => 'ក្រុម',
        ],

        'open_bulk_actions' => [
            'label' => 'សកម្មភាពភាគច្រើន',
        ],

        'column_manager' => [
            'label' => 'បិទ/បើកជួរឈរ',
        ],

    ],

    'empty' => [

        'heading' => 'គ្មាន​​ :model',

        'description' => 'បង្កើត​ :model មួយដើម្បីចាប់ផ្តើម។.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'អនុវត្តតម្រង',
            ],

            'remove' => [
                'label' => 'យកតម្រងចេញ',
            ],

            'remove_all' => [
                'label' => 'លុបចោលតម្រងទាំងអស់',
                'tooltip' => 'លុបចោលតម្រងទាំងអស់',
            ],

            'reset' => [
                'label' => 'កំណត់ឡើងវិញ',
            ],

        ],

        'heading' => 'តម្រង',

        'indicator' => 'តម្រងសកម្ម',

        'multi_select' => [
            'placeholder' => 'ទាំងអស់។',
        ],

        'select' => [
            'placeholder' => 'ទាំងអស់។',
        ],

        'trashed' => [

            'label' => 'កំណត់ត្រាដែលបានលុប',

            'only_trashed' => 'មានតែកំណត់ត្រាដែលបានលុបប៉ុណ្ណោះ។',

            'with_trashed' => 'ជាមួយនឹងកំណត់ត្រាដែលបានលុប',

            'without_trashed' => 'ដោយគ្មានកំណត់ត្រាដែលបានលុប',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'ដាក់ជាក្រុមដោយ',
                'placeholder' => 'ដាក់ជាក្រុមដោយ',
            ],

            'direction' => [

                'label' => 'ទិសដៅក្រុម',

                'options' => [
                    'asc' => 'ឡើង',
                    'desc' => 'ចុះ',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'អូស និងទម្លាក់កំណត់ត្រាតាមលំដាប់លំដោយ.',

    'selection_indicator' => [

        'selected_count' => 'បានជ្រើសរើស 1 កំណត់ត្រា|:count រាប់កំណត់ត្រា បានជ្រើសរើស',

        'actions' => [

            'select_all' => [
                'label' => 'ជ្រើសរើសទាំងអស់៖ :count',
            ],

            'deselect_all' => [
                'label' => 'ដកការជ្រើសរើសទាំងអស់',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'តម្រៀបតាម',
            ],

            'direction' => [

                'label' => 'តម្រៀបទិសដៅ',

                'options' => [
                    'asc' => 'ឡើង',
                    'desc' => 'ចុះ',
                ],

            ],

        ],

    ],

];
