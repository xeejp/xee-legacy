<?php
// return
return function ($parser, $data) {
    $return = $parser->parse($data['value']);
    return function () use ($parser) {
        $parser->ignore_parse = true;
        call_user_func($return);
    }
};

/* ex:
[
  'type' => 'return',
  'value' => [PARSABLE_OBJ],
]
*/
