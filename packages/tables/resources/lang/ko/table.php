<?php

return [

    'column_manager' => [

        'heading' => '열',

        'position' => ':count개 중 :position번째입니다.',

        'position_in_group' => ':group 그룹에서 :count개 중 :position번째입니다.',

        'reordered' => ':column 열이 :count개 중 :position번째 위치로 이동했습니다.',

        'reordered_deferred' => ':column 열이 :count개 중 :position번째 위치로 이동했습니다. 변경 사항을 저장하려면 열을 적용하세요.',

        'reordered_in_group' => ':column 열이 :group 그룹에서 :count개 중 :position번째 위치로 이동했습니다.',

        'reordered_in_group_deferred' => ':column 열이 :group 그룹에서 :count개 중 :position번째 위치로 이동했습니다. 변경 사항을 저장하려면 열을 적용하세요.',

        'actions' => [

            'apply' => [
                'label' => '열 적용',
            ],

            'move_up' => [
                'label' => ':column 열 위로 이동',
            ],

            'move_down' => [
                'label' => ':column 열 아래로 이동',
            ],

            'reorder' => [
                'label' => '열 순서 변경',
            ],

            'reset' => [
                'label' => '초기화',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => '작업|작업들',
        ],

        'editable' => [
            'saving' => '변경 사항 저장 중...',
            'saved' => '변경 사항이 저장되었습니다.',
            'failure' => '저장되었는지 확인할 수 없습니다. 다시 시도하기 전에 페이지를 새로고침하세요.',
            'unavailable' => '이 항목은 더 이상 수정할 수 없습니다.',
        ],

        'icon' => [

            'boolean' => [
                'true' => '예',
                'false' => '아니오',
            ],

        ],

        'select' => [

            'loading_message' => '로드 중...',

            'no_options_message' => '사용 가능한 옵션이 없습니다.',

            'no_search_results_message' => '검색 결과가 없습니다.',

            'placeholder' => '옵션을 선택하세요',

            'searching_message' => '검색 중...',

            'search_prompt' => '검색어를 입력하세요...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count 개 더 접기',
                'expand_list' => ':count 개 더 펼치기',
            ],

            'more_list_items' => ':count 항목이 더 있습니다',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '일괄 작업을 위해 모든 항목을 선택/선택 취소합니다.',
        ],

        'bulk_select_record' => [
            'label' => '일괄 작업을 위해 :key 항목을 선택/선택 취소합니다.',
        ],

        'bulk_select_group' => [
            'label' => '일괄 작업의 :title 그룹을 선택/선택 취소합니다.',
        ],

        'search' => [
            'label' => '검색',
            'placeholder' => '검색',
            'indicator' => '검색',
        ],

    ],

    'summary' => [

        'heading' => '요약',

        'subheadings' => [
            'all' => '모든 :label',
            'group' => ':group 요약',
            'page' => '이 페이지',
        ],

        'summarizers' => [

            'average' => [
                'label' => '평균',
            ],

            'count' => [
                'label' => '수량',
            ],

            'sum' => [
                'label' => '합계',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => '순서 변경 완료',
        ],

        'enable_reordering' => [
            'label' => '항목 순서 변경',
        ],

        'reorder_record' => [
            'label' => ':key 항목 순서 변경',
        ],

        'move_record_up' => [
            'label' => ':label 위로 이동',
        ],

        'move_record_down' => [
            'label' => ':label 아래로 이동',
        ],

        'filter' => [
            'label' => '필터',
        ],

        'group' => [
            'label' => '그룹',
        ],

        'open_bulk_actions' => [
            'label' => '일괄 작업',
        ],

        'column_manager' => [
            'label' => '열 전환',
        ],

        'toggle_record_content' => [
            'label' => ':key 항목 펼치기/접기',
        ],

    ],

    'empty' => [

        'heading' => ':model 없음',

        'description' => ':model 을(를) 만들어 시작하세요.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => '필터 적용',
            ],

            'remove' => [
                'label' => '필터 삭제',
            ],

            'remove_all' => [
                'label' => '모든 필터 삭제',
                'tooltip' => '모든 필터 삭제',
            ],

            'reset' => [
                'label' => '초기화',
            ],

        ],

        'heading' => '필터',

        'indicator' => '활성화된 필터',

        'multi_select' => [
            'placeholder' => '전체',
        ],

        'select' => [

            'placeholder' => '전체',

            'relationship' => [
                'empty_option_label' => '없음',
            ],

        ],

        'trashed' => [

            'label' => '삭제된 항목',

            'only_trashed' => '삭제된 항목만',

            'with_trashed' => '삭제된 항목 포함',

            'without_trashed' => '삭제된 항목 제외',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => '그룹 기준',
            ],

            'direction' => [

                'label' => '그룹 순서',

                'options' => [
                    'asc' => '오름차순',
                    'desc' => '내림차순',
                ],

            ],

        ],

    ],

    'loading' => '로드 중...',

    'reorder_indicator' => '항목을 순서대로 끌어다 놓습니다.',

    'reordering' => [
        'position' => ':label, :count개 중 :position번째입니다.',
        'moved' => ':label 항목이 :count개 중 :position번째 위치로 이동했습니다.',
    ],

    'result_count' => '{0} 결과 없음|{1} 결과 :count개|[2,*] 결과 :count개',

    'selection_indicator' => [

        'selected_count' => ':count 항목 선택됨',

        'actions' => [

            'select_all' => [
                'label' => ':count 항목 모두 선택',
            ],

            'deselect_all' => [
                'label' => '모두 선택 해제',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '정렬 기준',
            ],

            'direction' => [

                'label' => '정렬 순서',

                'options' => [
                    'asc' => '오름차순',
                    'desc' => '내림차순',
                ],

            ],

        ],

    ],

    'default_model_label' => '항목',

];
