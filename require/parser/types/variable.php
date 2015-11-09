<?php
// 変数を扱う
$variables = [];
return function ($parser, $data) use (&$variables) {
    $name = $data['name'];
    $value = isset($data['value'])? $parser->parse($data['value']): null;
    if ($value == null) {
        return function () use (&$variables, $name, $value) {
            if (!isset($variables[$name]))
                throw new Exception('Variable "'. $name .'" is undefined.');
            return $variables[$name];
        };
    } else {
        return function () use (&$variables, $name, $value) {
            return $variables[$name] = call_user_func($value);
        };
    }
};

/* ex:
[
  'type' => 'variable',
  'name' => 'abcval',
  ('value' => 100,) // valueが存在すればsetter、そうでなければgetterとして振る舞う
]
*/
