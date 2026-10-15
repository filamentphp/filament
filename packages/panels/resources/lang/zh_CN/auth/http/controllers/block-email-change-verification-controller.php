<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => '已阻止更换邮箱',
            'body' => '已阻止将邮箱更换为 :email。若非本人申请，请立即联系我们。',
        ],

        'failed' => [
            'title' => '未能阻止更换邮箱',
            'body' => '邮箱已完成验证并更换为 :email，无法再阻止此次更换。若非本人申请，请立即联系我们。',
        ],

    ],

];
