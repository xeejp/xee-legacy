<?php

class ExperimentModel extends Model{

    const PASSWORD_LENGTH = 6;
    const PASSWORD_ALPHABET = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ'; //'l' and 'O' is excluded

    private static $salt = 'bhi87fe';

    function insert($host_id, $game_id){
        $password = $this->create_password();
        return ['id' => $this->con->insert('experiment', ['host_id' => $host_id, 'game_id' => $game_id, 'password' => $this->hash_password($password)], true), 'password' => $password];
    }

    function get_experiment($password){
        return $this->con->fetch('SELECT `host_id`, `game_id`, `id` FROM `experiment` WHERE `password` = ?', $this->hash_password($password));
    }

    function hash_password($password){
        return sha256($password + self::$salt);
    }

    function check_password($password){
        if($this->con->get_count('experiment', ['password' => $this->hash_password($password)]) === 0){
            return false;
        }
        return true;
    }

    function create_password(){
        do{
            $password = random_str(self::PASSWORD_LENGTH, self::PASSWORD_ALPHABET);
        }while($this->check_password($password));
        return $password;
    }

}
