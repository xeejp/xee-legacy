<?php
// package: function
$functions = [];
return [
    'define' => function ($name, $function) use (&$functions) {
        if (!is_callable($function))
            throw new Exception('Arg is not function');
        $functions[$name] = $function;
    },
    'execute' => function ($name) use (&$functions) {
        if (!isset($functions[$name]))
            throw new Exception('Call undefined function');
        return call_user_func($functions[$name]);
    },
];
