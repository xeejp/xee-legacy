<?php
// ユーザ定義関数を返す
return function ($parser, $data) {
    $descriptions = $data['descriptions'];
    return function () use ($parser, $descriptions) {
        return function () use ($parser, $descriptions) {
            foreach ($descriptions as $description)
                call_user_func($parser->parse($description));
        };
    };
};

/* ex:
[
  'type' => 'user_function',
  'descriptions' => [
    [PARSABLE_OBJ],
    [PARSABLE_OBJ],
    [PARSABLE_OBJ], ...
  ],
]
*/
