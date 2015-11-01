<?php
// package: variable
$variables = [];
return [
    'set' => function ($name, $value) use (&$variables) {
        $variables[$name] = $value;
    },
    'get' => function ($name) use (&$variables) {
        if (!isset($variables[$name]))
            throw new Exception('variable is undefined');
        return $variable[$name];
    },
];
