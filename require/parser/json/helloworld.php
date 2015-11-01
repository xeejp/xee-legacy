<?php
return $json =
[ 'type' => 'procedure',
  'descriptions' => [
    // 画面に"Hello World!"と表示
    [ 'type' => 'function',
      'name' => 'system::echo',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'Hello World!'
        ],
      ],
    ],
    //
  ],
];

/*
// 雛形
[ 'type' => 'procedure',
  'descriptions' => [
    // ここにプログラムを記述 ※オブジェクトは全てパース可能でなければならない
  ]
]

// パース可能オブジェクトの基本形
[ 'type' => 'function' // typeに種類(constant, function, procedure等)を設定
  // type以外のオプションはtype毎に扱いが異なる(functionはnameとargs, constantはvalueが必要等)
]
// constant 例: 定数 100 の表現
[ 'type' => 'constant',
  'value' => 100
]
// function 例: 関数system::echo('helloworld')の呼び出し
[ 'type' => 'function',
  'name' => 'sytem::echo',
  'args' => [ // 引数を記述(引数は全て関数実行前に実行される)
    [ 'type' => 'constant',
      'value' => 'helloworld'
    ]
  ]
]
// 組み合わせ 例: ユーザ関数helloworld()を定義した後helloworld()を実行する
[ 'type' => 'procedure',
  'descriptions' => [
    // 関数 helloworld() を定義する
    [ 'type' => 'function',
      'name' => 'function::define', // function::define(function_name, function_obj)
      'args' => [
        [ 'type' => 'constant',
          'value' => 'helloworld'
        ],
        [ 'type' => 'user_function', // [type: user_function] descriptionsを実行するfunctionオブジェクトを返す
          'descriptions' => [
            // helloworld()の内容(画面にhelloworldを表示する等)
          ]
        ]
      ]
    ],
    // 関数 helloworld() を実行する
    [ 'type' => 'function',
      'name' => 'function::execute', // function::execute(function_name)
      'args' => [
        [ 'type' => 'constant',
          'value' => 'helloworld'
        ]
      ]
    ]
  ]
]
//

*/