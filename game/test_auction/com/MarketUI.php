<?php

class MarketUI extends ModUIComponent{

    private $template;
    private $item_list;

    public function __construct($template, $item_list){
        $this->template = $template;
        $this->item_list = $item_list;
    }

    public function get_template_name($name){
        return $name;
    }

    public function get_templates($name){
        return [$name => "{each ilist}{$this->template}<br/>{/each}"];
    }

    public function get_values($name){
        return ['ilist' => $this->item_list];
    }

    public function get_scripts($name){
        return [];
    }
    public function input($name, $value){}

}
