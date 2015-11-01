<?php
// package: modui
$controller = $this->get_controller();
return [
    'StaticUI' => function () use ($controller) {
        $string = '';
        $args = func_get_args();
        foreach ($args as $arg)
            $string .= $arg;
        $controller->add_component(new StaticUI($string));
    },
    'WebPageEdit' => function () use ($controller) {
        $controller->add_component(new WebPageEdit());
    },
    'OptionUI' => function ($var_name) use ($controller) {
        $controller->add_component(new OptionUI($controller, $var_name, ''));
    },
    'SendingUI' => function ($label, $var_name) use ($controller) {
        $controller->add_component(new SendingUI($label, function ($value) use ($var_name) {
            $controller->set($var_name, $value);
        }));
    },
];
