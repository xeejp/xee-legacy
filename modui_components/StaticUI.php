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
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-b", update);}'];
    }

    public function input($name, $value){
        dump($value, true);
        $this->_con->set('a', $value);
    }

}
