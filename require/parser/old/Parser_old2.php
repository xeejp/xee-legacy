<?php

class Parser {
    // value
    private static $values = [];
    public static function getValue ($name) {
        return self::$values[$name];
    }
    public static function setValue ($name, $value) {
        self::$values[$name] = $value;
    }

    // function
    private static $functions = [];
    public static function getFunction ($name) {
        return self::$functions[$name];
    }
    public static function setFunction ($name, $function) {
        self::$functions[$name] = $function;
    }

    public static function execute ($json) {
        foreach ($json as $module) {
            self::parse($module);
        }
    }
    public static function parse ($module) {
        // error
        if (!isset($module['type']))
            var_dump($module);
//            throw new Exception('Error: Type is undefined.');
        switch ($module['type']) {
        case 'function':
            return call_user_func(self::getFunction($module['name']), $module['value'], isset($module['option'])? $module['option']: []);
        case 'value':
            return self::getValue($module['name']);
        case 'constant':
            return $module['value'];
        case '': case null:
            break;
        default:
            // error
            throw new Exception('Error: Undefined Type "'. $module['type'] .'"');
        }
    }
}
