<?php

class ScriptUI extends ModUIComponent{
    private $scripts;

    public function __construct($scripts){
        $this->scripts = $scripts;
    }
    public function get_templates($name){
        return [$this->get_template_name($name) => ''];
    }
    public function get_scripts ($name) {
        return $this->scripts;
    }
}
