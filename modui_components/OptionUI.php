<?php

class OptionUI extends ModUIComponent{

    private $_con;
    private $var_name;
    private $value;

    public function __construct($con, $var_name, $default_value){
        $this->_con = $con;
        $this->var_name = $var_name;
        $this->value = $default_value;
    }

    public function get_template_name($name){
        return $name;
    }

    public function get_templates($name){
        $template = <<<TMPL
<input id="{_name}" type="text" value="{value}">
<button id="{_name}-b" class="pure-button">update</button>
TMPL;
        return [$name => $template];
    }

    public function get_values($name){
        $value = $this->_con->get($this->var_name, $this->value);
        return ['value' => $value];
    }

    public function get_scripts($name){
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-b", update);}'];
    }

    public function input($name, $value){
        $this->_con->set($this->var_name, $value);
    }

}
