<?php require('require/parser/Parser.php');

$json = require('require/parser/json/helloworld.php');

try {
    $parser = new Parser($_con);
    $parser->use_package('system');
    $parser->use_package('function');
    $parser->use_package('db');
    $parser->use_package('variable');
    $parser->use_package('modui');
    $parser->use_package('premade');
    $procedure = $parser->convert($json);
    call_user_func($procedure);
//    var_dump($procedure);
} catch (Exception $e) {
    echo($e->getMessage());
}
