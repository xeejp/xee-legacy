<?php

class GameModel extends Model{

    function get_all(){
        return $this->con->fetchAll('SELECT `id`, `name`, `directory` FROM `game`');
    }

    function get($id){
        return $this->con->fetch('SELECT `id`, `name`, `directory` FROM `game` WHERE `id` = ?', $id);
    }

    function exist_id($id){
        if($this->con->get_count('game', ['id' => $id]) === 1){
            return true;
        }
        return false;
    }
}
