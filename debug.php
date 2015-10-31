<?php require('require/parser/Parser.php');

$json = require('require/parser/json/sample.php');

try {
    $parser = new Parser($_con);
    $parser->use_package('system');
    $parser->use_package('function');
    $parser->use_package('db');
    $parser->use_package('modui');
    call_user_func($parser->parse($json));
} catch (Exception $e) {
    echo($e->getMessage());
}
