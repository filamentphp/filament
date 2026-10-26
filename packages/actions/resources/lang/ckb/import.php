<?php

return [

    'label' => ':label هاوردەی',

    'modal' => [

        'heading' => ':label هاوردەی',

        'form' => [

            'file' => [
                'label' => 'پەڕگەی',
                'placeholder' => 'بارکردنی پەڕگەی CSV',
                'rules' => [
                    'duplicate_columns' => '{0} پەڕگەکە نابێت لە یەک ستونی سەرەوەی زیاتری بەتاڵ بێت.|{1,*} پەڕگەکە نابێت ستونی بەتاڵی دووبارەی تێدابێت: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'ستونەکان',
                'placeholder' => 'هەڵبژاردنی ستون',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'داگرتنی نمونەی پەڕگەی CSV',
            ],

            'import' => [
                'label' => 'هاوردەکردن',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'هاوردەکردن تەواو بوو',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => '{1} داگرتنی زانیاریی لەبارەی ڕیزی هەڵە|[2,*] داگرتنی زانیاریی لەبارەی ڕیزە هەڵەکان',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'پەڕگەی CSV زۆر گەورەیە',
            'body' => '{1} نابێت لەیەک ڕیز زیاتر بەیەکجار هاوردەبکەیت.|[2,*] نابێت زیاتر لە :count ڕیز بەیەکجار هاوردەبکەیت.',
        ],

        'started' => [
            'title' => 'هاوردەکردن دەستیپێکرد',
            'body' => '{1} هاوردەکردن دەستیپێکردو ڕیزێک لە پشتەوە کاری لەسەر دەکرێت.|[2,*] هاوردەکردن دەستیپێکردو :count ڕیز لەپشتەوە کاری لەسەر دەکرێت.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'هەڵە',
        'system_error' => 'هەڵەی سیستەم, تکایە پەیوەندی بە پاڵپشتکارەوە.',
        'column_mapping_required_for_new_record' => 'ستونی :attribute-Spalte لەناو ستونێک لەناو پەڕگەکە ڕێکنەخرا, بەڵام بۆ زیادکردنی تۆمارێکی نوێ پێویستە.',
    ],

];
