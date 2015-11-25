<?php

class ButtonUI extends ModUIComponent{

    private $_con, $text_func, $input_func;

    public function __construct($_con, $text_func, $input_func){
        $this->_con = $_con;
        $this->text_func = $text_func;
        $this->input_func = $input_func;
    }

    public function get_templates($name){
        return [$this->get_template_name($name) => "<button id=\"{_name}\">{text}</button>"]; 
    }

    public function get_values($name){
        return ['text' => call_user_func($this->text_func, $this->_con)];
    }

    public function get_scripts($name){
        return ['event' => 'function(selector, update){ $(document).on("click", "#" + selector, update)}'];
    }

    public function input($name, $value){
        call_user_func($this->input_func, $this->_con);
    }

}
