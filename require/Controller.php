<?php

class Controller{

    private $vdb, $modui;

    public function __construct($vdb, $modui){
        $this->vdb = $vdb;
        $this->modui = $modui;
    }

    public function get_personal($name, $id=null, $default=null){
        $this->get('_participant_' . ($id != null ? $id : $this->participant['id']) . '_' . $name, $default);
    }

    public function set_personal($name, $value, $id=null){
        $this->get('_participant_' . ($id != null ? $id : $this->participant['id']) . '_' . $name, $value);
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
