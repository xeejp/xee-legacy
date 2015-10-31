<?php
// package: function
$parser = $this;
return [
    'define' => function ($name, $function) use ($parser) {
        $parser->packages['function']['_callback_'. $name] = $function;
    },
    'execute' => function ($name) use ($parser) {
        return call_user_func($parser->packages['function']['_callback_'. $name]);
    },
];
