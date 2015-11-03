<?php
// 定義済み関数を実行し値を返す
return function ($parser, $data) {
    list($package_name, $function_name) = explode('::', $data['name'], 2);
    $function = $parser->get_function($package_name, $function_name);
    $args = [];
    foreach ($data['args'] as $arg)
        $args[] = $parser->parse($arg);
    return function () use ($function, $args){
        $arguments = [];
        foreach ($args as $arg)
            $arguments[] = call_user_func($arg);
        return call_user_func_array($function, $arguments);
    };
};

/* ex:
[
  'type' => 'package',
  'name' => '<package_name>::<function_name>',
  'args' => [
    [PARSABLE_OBJ],
    [PARSABLE_OBJ],
    [PARSABLE_OBJ], ...
  ],
]
*/
