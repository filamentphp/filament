<?php

return [

    'label' => 'エクスポート',

    'modal' => [

        'heading' => 'エクスポート :label',

        'form' => [

            'columns' => [

                'label' => 'カラム',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column が有効になりました',
                    ],

                    'label' => [
                        'label' => ':column ラベル',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'エクスポート',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'エクスポート完了',

            'actions' => [

                'download_csv' => [
                    'label' => 'csv形式をダウンロード',
                ],

                'download_xlsx' => [
                    'label' => 'xlsx形式をダウンロード',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'エクスポートするデータが大きすぎます',
            'body' => '一度に:count行を超える行をエクスポートすることはできません。',
        ],

        'started' => [
            'title' => 'エクスポート開始',
            'body' => 'エクスポートが開始され、:count行がバックグラウンドで処理されます。',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
