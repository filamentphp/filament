<?php

return [

    'label' => 'Query builder',
    'form' => [

        'operator' => [

            'label' => 'Operator',
        ],
        'or_groups' => [

            'label' => 'ቡድኖች',
            'block' => [

                'label' => 'Disjunction (ወይም)',
                'or' => 'ወይም',
            ],
        ],
        'rules' => [

            'label' => 'ህጎች',
            'item' => [

                'and' => 'AND',
            ],
        ],
    ],
    'no_rules' => '(ምንም ህጎች የሉም)',
    'item_separators' => [

        'and' => 'AND',
        'or' => 'OR',
    ],
    'operators' => [

        'is_filled' => [

            'label' => [

                'direct' => 'ተሞልቶዋል',
                'inverse' => 'ባዶ ነው',
            ],
            'summary' => [

                'direct' => ':attribute ተሞልቶዋል',
                'inverse' => ':attribute ባዶ ነው',
            ],
        ],
        'boolean' => [

            'is_true' => [

                'label' => [

                    'direct' => 'እውነት ነው',
                    'inverse' => 'እውነት አይደለም',
                ],
                'summary' => [

                    'direct' => ':attribute እውነት ነው',
                    'inverse' => ':attribute እውነት አይደለም',
                ],
            ],
        ],
        'date' => [

            'is_after' => [

                'label' => [

                    'direct' => 'በኋላ የሆነ',
                    'inverse' => 'በኋላ አይደለም',
                ],
                'summary' => [

                    'direct' => ':attribute ከቀን :date በኋላ ነው',
                    'inverse' => ':attribute ከቀን :date በኋላ አይደለም',
                ],
            ],
            'is_before' => [

                'label' => [

                    'direct' => 'በፊት የሆነ',
                    'inverse' => 'በፊት ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute is before :date',
                    'inverse' => ':attribute is not before :date',
                ],
            ],
            'is_date' => [

                'label' => [

                    'direct' => 'ቀኑ የሆነ',
                    'inverse' => 'ቀኑ ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute :date ነው',
                    'inverse' => ':attribute :date አይደለም',
                ],
            ],
            'is_month' => [

                'label' => [

                    'direct' => 'ወሩ የሆነ',
                    'inverse' => 'ወሩ ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute :month ነው',
                    'inverse' => ':attribute :month አይደለም',
                ],
            ],
            'is_year' => [

                'label' => [

                    'direct' => 'አመቱ የሆነ',
                    'inverse' => 'አመቱ ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute is :year',
                    'inverse' => ':attribute is not :year',
                ],
            ],
            'form' => [

                'date' => [

                    'label' => 'ቀን',
                ],
                'month' => [

                    'label' => 'ወር',
                ],
                'year' => [

                    'label' => 'አመት',
                ],
            ],
        ],
        'number' => [

            'equals' => [

                'label' => [

                    'direct' => 'እኩል',
                    'inverse' => 'እኩል ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute :number ነው',
                    'inverse' => ':attribute ከ:number ጋር እኩል አይደለም',
                ],
            ],
            'is_max' => [

                'label' => [

                    'direct' => 'ከፍተኛ የሆነ',
                    'inverse' => 'የሚበልጥ',
                ],
                'summary' => [

                    'direct' => 'የ:attribute ትልቁ :number ነው',
                    'inverse' => ':attribute ከ:number ይበልጣል',
                ],
            ],
            'is_min' => [

                'label' => [

                    'direct' => 'ትንሽ የሆነ',
                    'inverse' => 'ያነሰ',
                ],
                'summary' => [

                    'direct' => 'የ:attribute ትንሹ :number ነው',
                    'inverse' => ':attribute ከ:number ያንሳል',
                ],
            ],
            'aggregates' => [

                'average' => [

                    'label' => 'አማካኝ',
                    'summary' => 'አማካኝ :attribute',
                ],
                'max' => [

                    'label' => 'ከፍተኛ',
                    'summary' => 'ከፍተኛ :attribute',
                ],
                'min' => [

                    'label' => 'ዝቅተኛ',
                    'summary' => 'ዝቅተኛ :attribute',
                ],
                'sum' => [

                    'label' => 'ድምር',
                    'summary' => 'የ:attribute ድምር',
                ],
            ],
            'form' => [

                'aggregate' => [

                    'label' => 'አጠቃላይ',
                ],
                'number' => [

                    'label' => 'ቁጥር',
                ],
            ],
        ],
        'relationship' => [

            'equals' => [

                'label' => [

                    'direct' => 'አለው',
                    'inverse' => 'የለውም',
                ],
                'summary' => [

                    'direct' => ':count :relationship አለው',
                    'inverse' => ':count :relationship የለውም',
                ],
            ],
            'has_max' => [

                'label' => [

                    'direct' => 'ቢበዛ ያለው',
                    'inverse' => 'የበለጠ ያለው',
                ],
                'summary' => [

                    'direct' => 'ከፍተኛው :count :relationship አለው',
                    'inverse' => 'ከ:count በላይ :relationship አለው',
                ],
            ],
            'has_min' => [

                'label' => [

                    'direct' => 'ዝቅተኛ ያለው',
                    'inverse' => 'ያነሰ ያለው',
                ],
                'summary' => [

                    'direct' => 'በትንሹ :count :relationship አለው',
                    'inverse' => 'ከ:count ያነሰ :relationship አለው',
                ],
            ],
            'is_empty' => [

                'label' => [

                    'direct' => 'ባዶ ነው',
                    'inverse' => 'ባዶ አይደለም',
                ],
                'summary' => [

                    'direct' => ':relationship ባዶ ነው',
                    'inverse' => ':relationship ባዶ አይደለም',
                ],
            ],
            'is_related_to' => [

                'label' => [

                    'single' => [

                        'direct' => 'ነው',
                        'inverse' => 'አይደለም',
                    ],
                    'multiple' => [

                        'direct' => 'ይዞዋል',
                        'inverse' => 'አልያዘም',
                    ],
                ],
                'summary' => [

                    'single' => [

                        'direct' => ':relationship is :values',
                        'inverse' => ':relationship is not :values',
                    ],
                    'multiple' => [

                        'direct' => ':relationship contains :values',
                        'inverse' => ':relationship does not contain :values',
                    ],
                    'values_glue' => [

                        0 => ', ',
                        'final' => ' or ',
                    ],
                ],
                'form' => [

                    'value' => [

                        'label' => 'ዋጋ',
                    ],
                    'values' => [

                        'label' => 'ዋጋዎች',
                    ],
                ],
            ],
            'form' => [

                'count' => [

                    'label' => 'ብዛት',
                ],
            ],
        ],
        'select' => [

            'is' => [

                'label' => [

                    'direct' => 'Is',
                    'inverse' => 'Is not',
                ],
                'summary' => [

                    'direct' => ':attribute is :values',
                    'inverse' => ':attribute is not :values',
                    'values_glue' => [

                        0 => ', ',
                        'final' => ' or ',
                    ],
                ],
                'form' => [

                    'value' => [

                        'label' => 'ዋጋ',
                    ],
                    'values' => [

                        'label' => 'ዋጋዎች',
                    ],
                ],
            ],
        ],
        'text' => [

            'contains' => [

                'label' => [

                    'direct' => 'የያዘ',
                    'inverse' => 'ያልያዘ',
                ],
                'summary' => [

                    'direct' => ':attribute :textን ይዞዋል',
                    'inverse' => ':attribute :textን አልያዘም',
                ],
            ],
            'ends_with' => [

                'label' => [

                    'direct' => 'ብሎ የሚያበቃ',
                    'inverse' => 'ብሎ የማያበቃ',
                ],
                'summary' => [

                    'direct' => ':attribute የሚያበቃው በ:text ነው',
                    'inverse' => ':attribute የሚያበቃው በ:text አይደለም',
                ],
            ],
            'equals' => [

                'label' => [

                    'direct' => 'እኩል የሆነ',
                    'inverse' => 'እኩል ያልሆነ',
                ],
                'summary' => [

                    'direct' => ':attribute ከ:text ጋር እኩል ነው',
                    'inverse' => ':attribute ከ:text ጋር እኩል እይደለም',
                ],
            ],
            'starts_with' => [

                'label' => [

                    'direct' => 'ብሎ የሚጀምር',
                    'inverse' => 'ብሎ የማይጀምር',
                ],
                'summary' => [

                    'direct' => ':attribute ከ:text ይጀምራል',
                    'inverse' => ':attribute ከ:text አይጀምርም',
                ],
            ],
            'form' => [

                'text' => [

                    'label' => 'ፅሁፍ',
                ],
            ],
        ],
    ],
    'actions' => [

        'add_rule' => [

            'label' => 'ህግ ጨምር',
        ],
        'add_rule_group' => [

            'label' => 'ህጎችን በቡድን ጨምር',
        ],
    ],
];
