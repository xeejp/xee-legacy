<?php

class StringUI extends ModUIComponent{

    private $_con;

    public function __construct($con){
        $this->_con = $con;
    }

    public function get_template_name($name){
        return 'string';
    }

    public function get_templates($name){
        $template = <<<TMPL
<input id="{_name}" type="text" value="{value}">
<button id="{_name}-b">aaaaa</button>
TMPL;
        return ['string' => $template];
    }

    public function get_values($name){
        return ['value' => $this->_con->get('a')];
    }

    public function get_scripts($name){
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-b", update);}'];
    }

    public function input($name, $value){
        dump($value, true);
        $this->_con->set('a', $value);
    }

}
