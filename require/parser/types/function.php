<?php
// 関数オブジェクトを返す
$this->use_type('return');
return function ($parser, $data) {
    if (!isset($data['descriptions']))
        throw new Exception('Undefined descriptions[function]');
    $descriptions = [];
    foreach ($data['descriptions'] as $description)
        $descriptions[] = $parser->parse($description);
    return function () use ($descriptions) {
        return function () use ($descriptions) {
            try {
                foreach ($descriptions as $description)
                    call_user_func($description);
            } catch (ReturnNotException $result) { // type:return のみが投げる例外をcatchする
                return $result->get_result();
            }
        };
    };
};

/* ex:
[
  'type' => 'function',
  'descriptions' => [
    [PARSABLE_OBJ],
    [PARSABLE_OBJ],
    [PARSABLE_OBJ], ...
  ],
]
*/
