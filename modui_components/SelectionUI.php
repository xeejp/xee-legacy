<?php

class SelectionUI extends ModUIComponent {
    private $button_title, $list, $sending;


    public function __construct($btn, $sending){
    }

    public function get_templates($name){
        $template = <<<TMPL
TMPL;
        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name){
        return [];
    }

    public function get_scripts($name){
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-b", update);}'];
    }

    public function input($name, $value){
        call_user_func($this->sending, $value);
    }

}
