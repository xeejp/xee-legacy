<?php

class XEE{

    function __construct(){
    }

    function return_game($template=NULL, $data=NULL){
        $result = ['info' => ['status' => 'not modified']]
        if($template !== NULL && $data !== NULL){
            
        }
        header('
        ehco json_encode($result);
        return 0;
    }

}
