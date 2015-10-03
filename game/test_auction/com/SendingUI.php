<?php

class SendingUI extends ModUIComponent{

    private $btn;
    private $sending;

    public function __construct($btn, $sending){
        $this->btn = $btn;
        $this->sending = $sending;
    }

    public function get_templates($name){
        $template = <<<TMPL
<input id="{_name}" type="text" value="">
<button id="{_name}-b">{$this->btn}</button><br/>
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
