<?php

return [

    'label' => 'インポート',

    'modal' => [

        'heading' => 'インポート :label',

        'form' => [

            'file' => [
                'label' => 'File',
                'placeholder' => 'CSVファイルをアップロード',
            ],

            'columns' => [
                'label' => 'カラム',
                'placeholder' => '列を選択',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'サンプルCSVファイルをダウンロード',
            ],

            'import' => [
                'label' => 'インポート',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'インポート完了',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => '失敗した行に関する情報のダウンロード',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'CSVファイルが大きすぎます',
            'body' => '一度に:count行を超える行をインポートすることはできません。',
        ],

        'started' => [
            'title' => 'インポート開始',
            'body' => 'インポートが開始され、:count行がバックグラウンドで処理されます。',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'エラー',
        'system_error' => 'システムエラーです。サポートにお問い合わせください。',
    ],

];
