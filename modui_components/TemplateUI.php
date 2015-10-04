<?php

class TemplateUI extends ModUIComponent{
    private $template, $values;

    public function __construct($template, $values){
        $this->template = $template;
        $this->values = $values;
    }
    public function get_templates($name){
        return [$this->get_template_name($name) => $this->template];
    }
    public function get_values($name){
        return $this->values;
    }
}
