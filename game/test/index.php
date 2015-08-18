<?php

class TestUI extends ModUIComponent{

    private $value;

    public function __construct($value){
        $this->value = $value;
    }

    public function get_template_name($name){
        return 'test';
    }

    public function get_templates($name){
        $template = <<<TMPL
<p>{value}</p>
<input id="{name}" type="text" value="{value}">
<button id="{name}-button">update</button>
TMPL;
        return ['test' => $template];
    }

    public function get_values($name){
        return ['value' => $this->value];
    }

    public function get_scripts($name){
        return ['function(selector){return $("#" + selector).val();}', 'function(update, selector){$(document).on("click", "#" + selector + "-button", update);}'];
    }

    public function input($name, $value){
    }

}

$_modui->add(new TestUI('It\'s a test.'));
