<?php

return [

    'label' => '쿼리 빌더',

    'form' => [

        'operator' => [
            'label' => '연산자',
        ],

        'or_groups' => [

            'label' => '그룹',

            'block' => [
                'label' => '배타적 논리 (OR)',
                'or' => '또는',
            ],

        ],

        'rules' => [

            'label' => '조건',

            'item' => [
                'and' => '그리고',
            ],

        ],

    ],

    'no_rules' => '(조건 없음)',

    'item_separators' => [
        'and' => '그리고',
        'or' => '또는',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => '채워짐',
                'inverse' => '빈 값',
            ],

            'summary' => [
                'direct' => ':attribute이(가) 채워짐',
                'inverse' => ':attribute이(가) 비어 있음',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => '참',
                    'inverse' => '거짓',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) 참',
                    'inverse' => ':attribute이(가) 거짓',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => '이후',
                    'inverse' => '이후가 아님',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :date 이후',
                    'inverse' => ':attribute이(가) :date 이후가 아님',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => '이전',
                    'inverse' => '이전이 아님',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :date 이전',
                    'inverse' => ':attribute이(가) :date 이전이 아님',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => '날짜임',
                    'inverse' => '날짜가 아님',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :date임',
                    'inverse' => ':attribute이(가) :date가 아님',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => '월임',
                    'inverse' => '월이 아님',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :month임',
                    'inverse' => ':attribute이(가) :month가 아님',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => '년도임',
                    'inverse' => '년도가 아님',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :year임',
                    'inverse' => ':attribute이(가) :year가 아님',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => '날짜',
                ],

                'month' => [
                    'label' => '월',
                ],

                'year' => [
                    'label' => '년도',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => '같음',
                    'inverse' => '같지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :number와 같음',
                    'inverse' => ':attribute이(가) :number와 같지 않음',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => '최대값임',
                    'inverse' => '보다 큼',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) 최대값 :number임',
                    'inverse' => ':attribute이(가) :number보다 큼',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => '최소값임',
                    'inverse' => '보다 작음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) 최소값 :number임',
                    'inverse' => ':attribute이(가) :number보다 작음',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => '평균',
                    'summary' => ':attribute의 평균',
                ],

                'max' => [
                    'label' => '최대값',
                    'summary' => ':attribute의 최대값',
                ],

                'min' => [
                    'label' => '최소값',
                    'summary' => ':attribute의 최소값',
                ],

                'sum' => [
                    'label' => '합계',
                    'summary' => ':attribute의 합계',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => '집계',
                ],

                'number' => [
                    'label' => '숫자',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => '포함함',
                    'inverse' => '포함하지 않음',
                ],

                'summary' => [
                    'direct' => ':count :relationship을(를) 가짐',
                    'inverse' => ':count :relationship을(를) 가지지 않음',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => '최대값을 가짐',
                    'inverse' => '보다 많음',
                ],

                'summary' => [
                    'direct' => '최대값 :count :relationship을(를) 가짐',
                    'inverse' => ':count보다 많은 :relationship을(를) 가짐',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => '최소값을 가짐',
                    'inverse' => '보다 작음',
                ],

                'summary' => [
                    'direct' => '최소값 :count :relationship을(를) 가짐',
                    'inverse' => ':count보다 작은 :relationship을(를) 가짐',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => '비어 있음',
                    'inverse' => '비어 있지 않음',
                ],

                'summary' => [
                    'direct' => ':relationship이(가) 비어 있음',
                    'inverse' => ':relationship이(가) 비어 있지 않음',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => '일치함',
                        'inverse' => '일치하지 않음',
                    ],

                    'multiple' => [
                        'direct' => '포함함',
                        'inverse' => '포함하지 않음',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship이(가) :values와 일치함',
                        'inverse' => ':relationship이(가) :values와 일치하지 않음',
                    ],

                    'multiple' => [
                        'direct' => ':relationship이(가) :values를 포함함',
                        'inverse' => ':relationship이(가) :values를 포함하지 않음',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' 또는 ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => '값',
                    ],

                    'values' => [
                        'label' => '값들',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => '개수',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => '일치함',
                    'inverse' => '일치하지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :values와 일치함',
                    'inverse' => ':attribute이(가) :values와 일치하지 않음',
                    'values_glue' => [
                        ', ',
                        'final' => ' 또는 ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => '값',
                    ],

                    'values' => [
                        'label' => '값들',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => '포함함',
                    'inverse' => '포함하지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :text를 포함함',
                    'inverse' => ':attribute이(가) :text를 포함하지 않음',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => '끝이 일치함',
                    'inverse' => '끝이 일치하지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :text로 끝남',
                    'inverse' => ':attribute이(가) :text로 끝나지 않음',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => '같음',
                    'inverse' => '같지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :text와 같음',
                    'inverse' => ':attribute이(가) :text와 같지 않음',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => '시작함',
                    'inverse' => '시작하지 않음',
                ],

                'summary' => [
                    'direct' => ':attribute이(가) :text로 시작함',
                    'inverse' => ':attribute이(가) :text로 시작하지 않음',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => '텍스트',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => '조건 추가',
        ],

        'add_rule_group' => [
            'label' => '조건 그룹 추가',
        ],

    ],

];
