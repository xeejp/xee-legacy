<?php
// 一連の処理を実行する
return function ($parser, $data) {
    if (!isset($data['descriptions']))
        throw new Exception('Undefined descriptions[procedure]');
    $descriptions = [];
    foreach ($data['descriptions'] as $description)
        $descriptions[] = $parser->parse($description);
    return function () use ($descriptions) {
        foreach ($descriptions as $description)
            call_user_func($description);
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
