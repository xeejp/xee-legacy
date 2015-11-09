<?php require(DIR_ROOT .'require/parser/Parser.php');

$json = '[{"type":"package","name":"premade::double_auction","args":[]}]';
try {
    $parser = new Parser($_con);
    $parser->use_package('premade');
    call_user_func($parser->convert($json));
} catch (Exception $e) {
    echo($e->getMessage());
}
