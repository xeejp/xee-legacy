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
    'OptionUI' => function () use ($controller) {
        $controller->add_component(new OptionUI($controller, 'test', null));
    },
];
