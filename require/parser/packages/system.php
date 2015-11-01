<?php
// package: system
return [
    'echo' => function () {
        foreach($args = func_get_args() as $arg)
            echo $arg;
    },
    'execute' => function ($function) {
        return call_user_func($function);
    },
    
    'if' => function ($bool, $ifTrue = null, $ifFalse = null) {
        if ($bool && is_callable($ifTrue))
            call_user_func($ifTrue);
        elseif (is_callable($ifFalse)) {
            call_user_func($ifFalse);
        }
    },
    'loop' => function ($times, $inner) {
        for ($i=0; $i<$times; $i++) {
            call_user_func($inner);
        }
    },
    'mt_rand' => function ($min, $max) {
        return mt_rand($min, $max);
    },
    'count' => function ($array) {
        return count($array);
    },
    'bool' => function ($val1, $condition, $val2) {
        switch ($condition) {
        case '>' : return $val1 >  $val2;
        case '<' : return $val1 <  $val2;
        case '>=': return $val1 >= $val2;
        case '<=': return $val1 <= $val2;
        case '==': return $val1 == $val2;
        case '!=': return $val1 != $val2;
        case '&&': return $val1 && $val2;
        case '||': return $val1 || $val2;
        }
    },
    'calc' => function ($val1, $symbol, $val2) {
        switch ($symbol) {
        case '+': return $val1 + $val2;
        case '-': return $val1 - $val2;
        case '*': return $val1 * $val2;
        case '/': return $val1 / $val2;
        case '%': return $val1 % $val2;
        case '&': return $val1 & $val2;
        case '|': return $val1 | $val2;
        }
    },
];
