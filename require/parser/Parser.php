<?php

class Parser {
    const TYPE_DIR_ROOT = 'types/';
    const PACKAGE_DIR_ROOT = 'packages/';
    private $controller;
    private $types = [];
    private $packages = [];

    public function __construct ($con) {
        $this->controller = $con;
        $this->use_type('constant');
        $this->use_type('variable');
        $this->use_type('function');
        $this->use_type('procedure');
        $this->use_type('user_function');
        $this->use_package('system');
    }

    public function use_type ($name) {
        if (!isset($this->types[$name]))
            $this->types[$name] = require(self::TYPE_DIR_ROOT. $name .'.php');
    }
    public function use_package ($name) {
        if (!isset($this->packages[$name]))
            $this->packages[$name] = require(self::PACKAGE_DIR_ROOT. $name .'.php');
    }

    public function parse ($data) {
        if (!isset($data['type']))
            throw new Exception('Type is undefined');
        if (!isset($this->types[$data['type']]))
            throw new Exception('Type "'. $data['type'] .'" is undefined');
        return $this->types[$data['type']]($this, $data);
    }

    public function get_function ($package_name, $function_name) {
        if (!isset($this->packages[$package_name]))
            throw new Exception('Package "'. $package_name .'" is undefined');
        if (!isset($this->packages[$package_name][$function_name]))
            throw new Exception('Function "'. $package_name .'::'. $function_name .'" is undefined');
        return $this->packages[$package_name][$function_name];
    }
    public function get_controller () {
        return $this->controller;
    }
}
