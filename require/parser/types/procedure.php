<?php
// 一連の処理を実行する
return function ($parser, $data) {
    if (!isset($data['descriptions'])) throw new Exception('Description is undefined');
    $descriptions = $data['descriptions'];
    return function () use ($parser, $descriptions) {
        foreach ($descriptions as $description) {
            call_user_func($parser->parse($description));
        }
    };
};

/* ex:
[
  'type' => 'procedure',
  'descriptions' => [
    [PARSABLE_OBJ],
    [PARSABLE_OBJ],
    [PARSABLE_OBJ], ...
  ],
]
*/
