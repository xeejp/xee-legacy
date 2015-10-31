<?php
// package: db
$controller = $this->get_controller();
return [
    'get' => function ($name) use ($controller) {
        return $controller->get($name, null);
    },
    'set' => function ($name, $value) use ($controller) {
        $controller->set($name, $value);
    },
    'get_personal' => function ($name, $id = null) use ($controller) {
        return $controller->get_personal($name, null, $id);
    },
    'set_personal' => function ($name, $value, $id = null) use ($controller) {
        $controller->set_personal($name, $value, $id);
    },
];
