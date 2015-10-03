<?php

class EventButton extends ModUIComponent{

    private $value;
    private $event;

    public function __construct($value='', $event=null){
        $this->value = $value;
        $this->event = ($event !== null)? $event: function($value){};
    }

    public function get_template_name($name){
        return $name;
    }

    public function get_templates($name){
        $template = <<<TMPL
<button id="{_name}">{value}</button>
TMPL;
        return [$name => $template];
    }

    public function get_values($name){
        return ['value' => ($this->value !== null)? $this->value: ''];
    }

    public function get_scripts($name){
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector, update);}'];
    }

    public function input($name, $value){
        call_user_func($this->event, $value);
    }

}
