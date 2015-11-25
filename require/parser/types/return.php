<?php
// 例外をthrowする擬似的なreturn
return function ($parser, $data) {
    if (!isset($data['value']))
        throw new Exception('Undefined value');
    $result = $parser->parse($data['value']);
    return function () use ($result) {
        throw new ReturnNotException(call_user_func($result));
    };
};
// returnをthrowするための例外もどき
class ReturnNotException extends Exception {
    private $result;
    public function __construct($result){
        $this->result = $result;
    }
    public function get_result () {
        return $this->result;
    }
}

/* ex:
[
  'type' => 'return',
  'value' => [PARSABLE_OBJ],
  ],
]
*/
