<?php

class TemplateUI extends ModUIComponent{
    private $template, $values;

    public function __construct($template, $values=null, $scripts=null){
        $this->template = $template;
        $this->values = $values;
        $this->scripts = $scripts;
    }
    public function get_templates($name){
        return [$this->get_template_name($name) => $this->template];
    }
    public function get_values($name){
        return is_callable($this->values)? call_user_func($this->values): [];
    }
    public function get_scripts($name){
        return is_callable($this->scripts)? call_user_func($this->scripts): [];
    }
}
