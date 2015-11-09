<?php
$json =
[ // 画面に"Hello World!"と表示
    [ 'type' => 'package',
      'name' => 'system::echo',
      'args' => [ // 引数を記述(引数は全て関数実行前に実行される)
        [ 'type' => 'constant',
          'value' => 'helloworld'
        ]
      ]
    ]
];

return json_encode($json);
/*

// 雛形
$json = [
    // ここにプログラムを記述 ※オブジェクトは全てパース可能でなければならない
]

// パース可能オブジェクトの基本形
[ 'type' => 'package' // typeにオブジェクトの種類(constant(定数を作る), package(定義済み関数の呼び出し), function(関数を作る), procedure(即時実行)等)を設定
  // type以外のオプションはtype毎に扱いが異なる(packageはnameとargs, constantはvalueが必要等)
]

// constant 例: 定数 100 の表現
[ 'type' => 'constant',
  'value' => 100
]
// package 例: 関数system::echo('helloworld')の呼び出し
[ 'type' => 'package',
  'name' => 'system::echo',
  'args' => [ // 引数を記述(引数は全て関数実行前に実行される)
    [ 'type' => 'constant',
      'value' => 'helloworld'
    ]
  ]
]
// 組み合わせ 例: ユーザ関数helloworld()を定義した後helloworld()を実行する一連の処理を行う
[ 'type' => 'procedure',
  'descriptions' => [
    // 関数 helloworld() を定義する
    [ 'type' => 'package',
      'name' => 'function::define', // function::define(function_name, function_obj)
      'args' => [
        [ 'type' => 'constant',
          'value' => 'helloworld'
        ],
        [ 'type' => 'function', // [type: user_function] descriptionsを実行するfunctionオブジェクトを返す
          'descriptions' => [
            // helloworld()の内容(画面にhelloworldを表示する等)
          ]
        ]
      ]
    ],
    // 関数 helloworld() を実行する
    [ 'type' => 'package',
      'name' => 'function::execute', // function::execute(function_name)
      'args' => [
        [ 'type' => 'constant',
          'value' => 'helloworld'
        ]
      ]
    ]
  ]
]

*/