<?php

class Controller{

    private $vdb, $modui;
    const PAGE = '_page';

    public function __construct($vdb, $modui){
        $this->vdb = $vdb;
        $this->modui = $modui;
    }

    public function set_page($page){
        $this->set(self::PAGE, $page);
    }

    public function get_page($page='default'){
        $this->get(self::PAGE, $page);
    }

    public function get($name, $default=null){
        return $this->vdb->get($name, $default);
    }

    public function set($name, $value){
        $this->vdb->set($name, $value);
    }

    public function add_component($component, $hook=null){
        $this->modui->add($component, $hook);
    }

}
