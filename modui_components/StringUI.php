<?php

class StringUI extends ModUIComponent{

    private $value;

    public function __construct($value){
        $this->value = $value;
    }

    public function get_template_name($name){
        return 'string';
    }

    public function get_templates($name){
        $template = <<<TMPL
<input id="{name}" type="text" value="{value}">
TMPL;
        return ['string' => $template];
    }

    public function get_values($name){
        return ['value' => $this->value];
    }

    public function get_scripts($name){
        return ["function(selector){return $(\"#$name\").val();}", ''];
    }

    public function input($name, $value){
    }

}
