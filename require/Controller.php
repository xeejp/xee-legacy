<?php

class Controller{

    private $vdb, $modui;

    public function __construct($vdb, $modui){
        $this->vdb = $vdb;
        $this->modui = $modui;
    }

    public function get_personal($name, $default=null, $id=null){
        return $this->get('_participant_' . ($id != null ? $id : $this->participant['id']) . '_' . $name, $default);
    }

    public function set_personal($name, $value, $id=null){
        $this->set('_participant_' . ($id != null ? $id : $this->participant['id']) . '_' . $name, $value);
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

    public function load(){
        foreach(func_get_args() as $name){
            if(is_array($name)){
                $this->{$name[0]} = $this->get($name[0], $name[1]);
            }else{
                $this->{$name} = $this->get($name);
            }
        }
    }

    public function load_personal(){
        foreach(func_get_args() as $name){
            if(is_array($name)){
                $this->{$name[0]} = $this->get_personal($name[0], $name[1]);
            }else{
                $this->{$name} = $this->get_personal($name);
            }
        }
    }

    public function load_personal_all(){
        $args = func_get_args();
        foreach($this->participants as $key => $participant){
            foreach($args as $name){
                if(is_array($name)){
                    $this->participants[$key][$name[0]] = $this->get_personal($name[0], $name[1], $participant['id']);
                    if(isset($this->participant) && $this->participant['id'] === $participant['id']) $this->participant[$name[0]] = $this->participants[$key][$name[0]];
                }else{
                    $this->participants[$key][$name] = $this->get_personal($name, null, $participant['id']);
                    if(isset($this->participant) && $this->participant['id'] === $participant['id']) $this->participant[$name] = $this->participants[$key][$name];
                }
            }
        }
    }

    public function filter_participants($func){
        $result = [];
        foreach($this->participants as $participant){
            $tmp = [];
            foreach($func($this, $participant) as $name){
                if(is_array($name)){
                    $tmp[$name[0]] = $name[1];
                }else{
                    $tmp[$name] = $participant[$name];
                }
            }
            if(count($tmp) != 0){
                $result[] = $tmp;
            }
        }
        return $result;
    }

    public function load_file($file) {
        return load_static($file);
    }

}
