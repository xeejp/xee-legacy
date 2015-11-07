<?php

class DataContainer implements IteratorAggregate {
    // constant
    const PREFIX = '_data_', DELIMITER = "__";
    // property
    private $controller, $name;
    private $data = [];

    // constructor
    public function __construct ($controller, $name) {
        $this->name = $name;
        $this->controller = $controller;
        $this->open();
        register_shutdown_function(
            function () {
                $this->close();
            }
        );
    }
    // getter setter
    public function get ($key) {
        return isset($this->data[$key])? $this->data[$key]: null;
    }
    public function set ($key, $value) {
        $this->data[$key] = $value;
    }
    public function &get_ref () {
        return $this->data;
    }
    public function set_ref (&$data) {
        $this->data = $data;
    }
    // names
    private function get_name ($name) {
        return self::PREFIX. $this->name .self::DELIMITER. $name;
    }
    private function get_name_length () {
        return $this->get_name('len');
    }
    private function get_name_key ($no) {
        return $this->get_name('key') .self::DELIMITER. sprintf('%06d', $no);
    }
    private function get_name_value ($no) {
        return $this->get_name('val') .self::DELIMITER. sprintf('%06d', $no);
    }
    // util
    private function set_tree ($key, $value) {
        $current = &$this->data;
        $indexes = explode(self::DELIMITER, $key);
        foreach ($indexes as $index) {
            if (!isset($current[$index]))
                $current[$index] = [];
            $current = &$current[$index];
        }
        $current = $value;
    }
    private function get_linear ($data, $key_prefix='') {
        $linear = [];
        foreach ($data as $key => $value) {
            $new_key = $key_prefix .self::DELIMITER. $key;
            if (is_array($value))
                $linear = array_merge($linear, $this->get_linear($value, $new_key));
            else
                $linear[$new_key] = $value;
        }
        return $linear;
    }
    // load
    private function get_length () {
        return $this->controller->get($this->get_name_length(), 0);
    }
    private function get_key ($no) {
        return $this->controller->get($this->get_name_key($no), '');
    }
    private function get_value ($no) {
        return $this->controller->get($this->get_name_value($no), '');
    }
    private function open () {
        $length = $this->get_length();
        for($i=0; $i<$length; $i++)
            $this->set_tree($this->get_key($i), $this->get_value($i));
    }
    // save
    private function set_length ($length) {
        return $this->controller->set($this->get_name_length(), $length);
    }
    private function set_key ($no, $key) {
        return $this->controller->set($this->get_name_key($no), $key);
    }
    private function set_value ($no, $value) {
        return $this->controller->set($this->get_name_value($no), $value);
    }
    private function close () {
        $linear = [];
        foreach ($this->data as $key => $value)
            $linear = array_merge($linear, $this->get_linear($value, $key));
        $this->set_length(count($linear));
        $i = 0;
        foreach ($linear as $key => $value) {
            $this->set_key($i, $key);
            $this->set_value($i, $value);
            $i++;
        }
    }
    // implements IteratorAggregate
    public function getIterator() {
        return new ArrayIterator($this->data);
    }
}
