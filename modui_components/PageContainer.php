<?php

class PageContainer extends NormalContainer{

    private $page;

    public function __construct($page){
        parent::__construct();
        $this->page = $page;
    }

    public function add($page, $component, $hook_function=null){
        $this->components[$page] = $component;
        if($hook_function !== null){
            $this->hooks[$page] = $hook_function;
        }else{
            $this->hooks[$page] = function(){};
        }
    }

    protected function get_template($name){
        $template = '{switch _page}';
        foreach($this->components as $key => $component){
            $template .= "{case $key}";
            $template .= ModUI::get_lwte_use($component->get_template_name(ModUI::get_child_name($name, $key)), ModUI::get_child_name($name, $key));
        }
        $template .= '{/switch}';
        return $template;
    }

    public function get_values($name){
        $values = ['_name' => $name, '_page' => $this->page];
        $values[ModUI::get_child_name($name, $this->page)] =
            array_merge(['_name' => ModUI::get_child_name($name, $this->page), '_template_name' => $this->components[$this->page]->get_template_name(ModUI::get_child_name($name, $this->page))],
                $this->components[$this->page]->get_values(ModUI::get_child_name($name, $this->page)));
        return $values;
    }

    public function input($name, $value){
        $result = ModUI::get_name($name);
        if($result[0] == $this->page){
            parent::input($name, $value);
        }
    }
}
