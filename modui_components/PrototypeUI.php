<?php

class PrototypeUI extends ModUIComponent{

    public $_con;
    private $args;

    public function __construct($con, $args){
        $this->_con = $con;
        $this->args = $args;
    }

    public function get_template_name($name){
        return (isset($this->args['template_name']))? $this->args['template_name']($this, $name): $name;
    }

    public function get_templates($name){
        return $this->args['templates']($this, $name);
    }

    public function get_values($name){
        return (isset($this->args['values']))? $this->args['values']($this, $name): [];
    }

    public function get_scripts($name){
        return (isset($this->args['scripts']))? $this->args['scripts']($this, $name): [];
    }

    public function input($name, $value){
        if(isset($this->args['input'])) $this->args['input']($this, $name, $value);
    }

}
