<?php
// ローカル変数を扱う
$local_variables = [];
return function ($parser, $data) use (&$local_variables) {
    $name = $data['name'];
    $value = isset($data['value'])? $parser->parse($data['value']): null;
    if ($value == null) {
        return function () use (&$local_variables, $name, $value) {
            if (!isset($local_variables[$name]))
                throw new Exception('Variable "'. $name .'" is undefined.');
            return $local_variables[$name];
        };
    } else {
        return function () use (&$local_variables, $name, $value) {
            return $local_variables[$name] = call_user_func($value);
        };
    }
};

/* ex:
[
  'type' => 'variable',
  'name' => 'abcval',
  ('value' => 100,) // valueが定義されていればsetter、そうでなければgetterとして振る舞う
]
*/
