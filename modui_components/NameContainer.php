<?php

class NameContainer extends NormalContainer{
    
    public function add($name, $component, $hook_function=null){
        $this->components[$name] = $component;
        if($hook_function !== null){
            $this->hooks[$name] = $hook_function;
        }else{
            $this->hooks[$name] = function(){};
        }
    }

}
