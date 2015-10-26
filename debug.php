<?php require 'Parser.php';

$_con->{'participants'} = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
$_con->{'participant'} = [
    'id' => 1,
    'name' => 1
];

Parser::setValue('user', $_con->participants);

// functions
Parser::setFunction('if', function ($value, $option) {
    if (Parser::parse($value))
        Parser::execute($option);
});
Parser::setFunction('loop', function ($value, $option) {
    $num = Parser::parse($value);
    for ($i=0; $i<$num; $i++)
        Parser::execute($option);
});
Parser::setFunction('count', function ($value) {
    return count(Parser::getValue($value));
});
Parser::setFunction('display', function ($value) use ($_con) {
    $_con->add_component(new StaticUI(Parser::parse($value) .'<br/>'));
});
Parser::setFunction('concat', function ($value) use ($_con) {
    $str = '';
    foreach ($value as $val)
        $str .= Parser::parse($val);
    return $str;
});
Parser::setFunction('getBool', function ($value) {
    $vals[0] = Parser::parse($value[0]);
    $vals[1] = Parser::parse($value[1]);
    $vals[2] = Parser::parse($value[2]);
    switch ($vals[1]) {
    case '>': return $vals[0] > $vals[2];
    case '<': return $vals[0] < $vals[2];
    case '>=': return $vals[0] >= $vals[2];
    case '<=': return $vals[0] <= $vals[2];
    case '==': return $vals[0] == $vals[2];
    case '!=': return $vals[0] != $vals[2];
    case '&&': return $vals[0] && $vals[2];
    case '||': return $vals[0] || $vals[2];
    default: break;
    }
});

// DB Access
Parser::setFunction('getVar', function ($value) use ($_con) {
    return $_con->get(Parser::parse($value[0]));
});
Parser::setFunction('setVar', function ($value) use ($_con) {
    $_con->set(Parser::parse($value[0]), Parser::parse($value[1]));
});
Parser::setFunction('getPersonalVar', function ($value) use ($_con) {
    return $_con->get_personal(Parser::parse($value[0]));
});
Parser::setFunction('setPersonalVar', function ($value) use ($_con) {
    $_con->set_personal(Parser::parse($value[0]), Parser::parse($value[1]));
});
// sys
Parser::setFunction('defineFunc', function ($value, $option) {
    Parser::setFunction(Parser::parse($value), function () use ($option) {
        Parser::execute($option);
    });
});
Parser::setFunction('mt_rand', function ($value) {
    return mt_rand(Parser::parse($value[0]), Parser::parse($value[1]));
});
Parser::setFunction('calc', function ($value) {
    $vals[0] = Parser::parse($value[0]);
    $vals[1] = Parser::parse($value[1]);
    $vals[2] = Parser::parse($value[2]);
    switch ($vals[1]) {
    case '+': return $vals[0] + $vals[2];
    case '-': return $vals[0] - $vals[2];
    case '*': return $vals[0] * $vals[2];
    case '/': return $vals[0] / $vals[2];
    case '%': return $vals[0] % $vals[2];
    case '&': return $vals[0] & $vals[2];
    case '|': return $vals[0] | $vals[2];
    default: break;
    }
});

// execute
$json = [
    [ // 価格を表示するサブルーチンを定義する
        'type' => 'function',
        'name' => 'defineFunc',
        'value' => [
            'type' => 'constant',
            'value' => 'display_price'
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'function',
                    'name' => 'getPersonalVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'price',
                        ]
                    ]
                ]
            ]
        ]
    ], [ // 初期化
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'getPersonalVar',
                    'value' => [                    
                        [
                            'type' => 'constant',
                            'value' => 'init'
                        ]
                    ]
                ], [
                    'type' => 'constant',
                    'value' => '!='
                ], [
                    'type' => 'constant',
                    'value' => true
                ]
            ],
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'setPersonalVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'price'
                    ], [
                        'type' => 'constant',
                        'value' => 600
                    ]
                ]
            ], [
                'type' => 'function',
                'name' => 'setPersonalVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'init'
                    ], [
                        'type' => 'constant',
                        'value' => true
                    ]
                ]
            ]
        ],
    ], [ // user数表示
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'function',
            'name' => 'concat',
            'value' => [
                [
                    'type' => 'constant',
                    'value' => 'Users: ',
                ], [
                    'type' => 'function',
                    'name' => 'count',
                    'value' => 'user',
                ]
            ]
        ]
    ], [ // もしユーザ数が40以上なら価格を表示
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'count',
                    'value' => 'user',
                ], [
                    'type' => 'constant',
                    'value' => '>='
                ], [
                    'type' => 'constant',
                    'value' => 40
                ]
            ]
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'constant',
                    'value' => 'userが40人以上です'
                ]
            ], [
                'type' => 'function',
                'name' => 'loop',
                'value' => [
                    'type' => 'function',
                    'name' => 'getPersonalVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'price',
                        ]
                    ]
                ],
                'option' => [
                    [
                        'type' => 'function',
                        'name' => 'display_price',
                        'value' => []
                    ]
                ]
            ]
        ]
    ], [ // もしユーザ数が40以上でなければ
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'count',
                    'value' => 'user',
                ], [
                    'type' => 'constant',
                    'value' => '<'
                ], [
                    'type' => 'constant',
                    'value' => 40
                ]
            ]
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'constant',
                    'value' => 'userが40人未満です'
                ]
            ]
        ]
    ]
];

//Parser::execute($json);


































$game = [
    [ // 1から6までのランダムな数値を変数[rand]に代入するサブルーチンを定義
        'type' => 'function',
        'name' => 'defineFunc',
        'value' => [
            'type' => 'constant',
            'value' => 'u_get_rand'
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'setVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'rand'
                    ], [
                        'type' => 'function',
                        'name' => 'mt_rand',
                        'value' => [
                            [
                                'type' => 'constant',
                                'value' => 1
                            ], [
                                'type' => 'constant',
                                'value' => 6
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ], [ // 勝利
        'type' => 'function',
        'name' => 'defineFunc',
        'value' => [
            'type' => 'constant',
            'value' => 'u_win'
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'setVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'win'
                    ], [
                        'type' => 'function',
                        'name' => 'calc',
                        'value' => [
                            [
                                'type' => 'function',
                                'name' => 'getVar',
                                'value' => [
                                    [
                                        'type' => 'constant',
                                        'value' => 'win'
                                    ]
                                ]
                            ], [
                                'type' => 'constant',
                                'value' => '+'
                            ], [
                                'type' => 'constant',
                                'value' => '1'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ], [ // 敗北
        'type' => 'function',
        'name' => 'defineFunc',
        'value' => [
            'type' => 'constant',
            'value' => 'u_lose'
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'setVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'lose'
                    ], [
                        'type' => 'function',
                        'name' => 'calc',
                        'value' => [
                            [
                                'type' => 'function',
                                'name' => 'getVar',
                                'value' => [
                                    [
                                        'type' => 'constant',
                                        'value' => 'lose'
                                    ]
                                ]
                            ], [
                                'type' => 'constant',
                                'value' => '+'
                            ], [
                                'type' => 'constant',
                                'value' => '1'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ], [ // 引き分け
        'type' => 'function',
        'name' => 'defineFunc',
        'value' => [
            'type' => 'constant',
            'value' => 'u_drow'
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'setVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'drow'
                    ], [
                        'type' => 'function',
                        'name' => 'calc',
                        'value' => [
                            [
                                'type' => 'function',
                                'name' => 'getVar',
                                'value' => [
                                    [
                                        'type' => 'constant',
                                        'value' => 'drow'
                                    ]
                                ]
                            ], [
                                'type' => 'constant',
                                'value' => '+'
                            ], [
                                'type' => 'constant',
                                'value' => '1'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ], [ // 説明
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'constant',
            'value' => 'サイコロ勝負(出目の大きい方が勝ち)',
        ]
    ], [ // サイコロを振る (player)
        'type' => 'function',
        'name' => 'u_get_rand',
        'value' => [],
    ], [ // player の強さ決定
        'type' => 'function',
        'name' => 'setVar',
        'value' => [
            [
                'type' => 'constant',
                'value' => 'player'
            ], [
                'type' => 'function',
                'name' => 'getVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'rand'
                    ]
                ]
            ]
        ]
    ], [ // 表示 (player)
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'function',
            'name' => 'concat',
            'value' => [
                [
                    'type' => 'constant',
                    'value' => 'あなたの目は',
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'player'
                        ]
                    ],
                ], [
                    'type' => 'constant',
                    'value' => 'です',
                ], 
            ]
        ]
    ], [ // サイコロを振る (enemy)
        'type' => 'function',
        'name' => 'u_get_rand',
        'value' => [],
    ], [ // enemy の強さ決定
        'type' => 'function',
        'name' => 'setVar',
        'value' => [
            [
                'type' => 'constant',
                'value' => 'enemy'
            ], [
                'type' => 'function',
                'name' => 'getVar',
                'value' => [
                    [
                        'type' => 'constant',
                        'value' => 'rand'
                    ]
                ]
            ]
        ]
    ], [ // 表示 (enemy)
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'function',
            'name' => 'concat',
            'value' => [
                [
                    'type' => 'constant',
                    'value' => '相手の目は',
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'enemy'
                        ]
                    ],
                ], [
                    'type' => 'constant',
                    'value' => 'です',
                ], 
            ]
        ]
    ], [ // 勝利
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'player'
                        ]
                    ]
                ], [
                    'type' => 'constant',
                    'value' => '>'
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'enemy'
                        ]
                    ]
                ]
            ]
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'constant',
                    'value' => 'あなたの勝ち',
                ]
            ], [
                'type' => 'function',
                'name' => 'u_win',
                'value' => []
            ]
        ]
    ], [ // 敗北
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'player'
                        ]
                    ]
                ], [
                    'type' => 'constant',
                    'value' => '<'
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'enemy'
                        ]
                    ]
                ]
            ]
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'constant',
                    'value' => '相手の勝ち',
                ]
            ], [
                'type' => 'function',
                'name' => 'u_lose',
                'value' => []
            ]
        ]
    ], [ // 引き分け
        'type' => 'function',
        'name' => 'if',
        'value' => [
            'type' => 'function',
            'name' => 'getBool',
            'value' => [
                [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'player'
                        ]
                    ]
                ], [
                    'type' => 'constant',
                    'value' => '=='
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'enemy'
                        ]
                    ]
                ]
            ]
        ],
        'option' => [
            [
                'type' => 'function',
                'name' => 'display',
                'value' => [
                    'type' => 'constant',
                    'value' => '引き分け',
                ]
            ], [
                'type' => 'function',
                'name' => 'u_drow',
                'value' => []
            ]
        ]
    ], [ // 戦績を表示
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'function',
            'name' => 'concat',
            'value' => [
                [
                    'type' => 'constant',
                    'value' => '勝利: ',
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'win'
                        ]
                    ],
                ], [
                    'type' => 'constant',
                    'value' => '回　',
                ], [
                    'type' => 'constant',
                    'value' => '敗北: ',
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'lose'
                        ]
                    ],
                ], [
                    'type' => 'constant',
                    'value' => '回　',
                ], [
                    'type' => 'constant',
                    'value' => '引分: ',
                ], [
                    'type' => 'function',
                    'name' => 'getVar',
                    'value' => [
                        [
                            'type' => 'constant',
                            'value' => 'drow'
                        ]
                    ],
                ], [
                    'type' => 'constant',
                    'value' => '回',
                ], 
            ]
        ]
    ], [ // 勝率計算
        'type' => 'function',
        'name' => 'display',
        'value' => [
            'type' => 'function',
            'name' => 'concat',
            'value' => [
                [
                    'type' => 'constant',
                    'value' => '勝率: ',
                ], [
                    'type' => 'function',
                    'name' => 'calc',
                    'value' => [
                        [
                            'type' => 'function',
                            'name' => 'calc',
                            'value' => [
                                [
                                    'type' => 'function',
                                    'name' => 'getVar',
                                    'value' => [
                                        [
                                            'type' => 'constant',
                                            'value' => 'win'
                                        ]
                                    ]
                                ], [
                                    'type' => 'constant',
                                    'value' => '/'
                                ], [
                                    'type' => 'function',
                                    'name' => 'calc',
                                    'value' => [
                                        [
                                            'type' => 'function',
                                            'name' => 'getVar',
                                            'value' => [
                                                [
                                                    'type' => 'constant',
                                                    'value' => 'win'
                                                ]
                                            ]
                                        ], [
                                            'type' => 'constant',
                                            'value' => '+'
                                        ], [
                                            'type' => 'function',
                                            'name' => 'calc',
                                            'value' => [
                                                [
                                                    'type' => 'function',
                                                    'name' => 'getVar',
                                                    'value' => [
                                                        [
                                                            'type' => 'constant',
                                                            'value' => 'lose'
                                                        ]
                                                    ]
                                                ], [
                                                    'type' => 'constant',
                                                    'value' => '+'
                                                ], [
                                                    'type' => 'function',
                                                    'name' => 'getVar',
                                                    'value' => [
                                                        [
                                                            'type' => 'constant',
                                                            'value' => 'drow'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ], [
                            'type' => 'constant',
                            'value' => '*'
                        ], [
                            'type' => 'constant',
                            'value' => 100
                        ]
                    ]
                ], [
                    'type' => 'constant',
                    'value' => '%',
                ], [
                    'type' => 'constant',
                    'value' => '<br/>------------------------------------------------------------<br/>',
                ]
            ]
        ]
    ]
];

Parser::execute([
    [
        'type' => 'function',
        'name' => 'loop',
        'value' => [
            'type' => 'constant',
            'value' => 100
        ],
        'option' => $game
    ]
]);
