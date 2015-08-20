<?php

class StaticUI extends ModUIComponent{

    private $template;

    public function __construct($template){
        $this->template = $template;
    }

    public function get_template_name($name){
        return $name;
    }

    public function get_templates($name){
        $template = $this->template;
        return [$name => $template];
    }

    public function get_values($name){
        return [];
    }

    public function get_scripts($name){
        return [];
    }

    public function input($name, $value){
    }

}
