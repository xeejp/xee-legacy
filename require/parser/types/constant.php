<?php
// 定数を返す
return function ($parser, $data) {
    $value = $data['value'];
    return function () use ($value) {
        return $value;
    };
};

/* ex:
[
  'type' => 'constant',
  'value' => 100,
]
*/
