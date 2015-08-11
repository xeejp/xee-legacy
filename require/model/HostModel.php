<?php

class HostModel extends Model{

    const NAME_MIN = 1, NAME_MAX = 32, PASSWORD_MIN = 4, PASSWORD_MAX = 64;

    private static $salt = 'iaw$#ji!""fd;-';
    private static $salt2 = 'llll#"fwwFSElll$#JI!';

    function exist_id($id){
        if($this->con->get_count('host', ['id' => $id]) === 1){
            return true;
        }
        return false;
    }

    function exist_name($name){
        if($this->con->get_count('host', ['name' => $name]) === 1){
            return true;
        }
        return false;
    }

    function check_available($name, $password){
        $name_len = strlen($name);
        $pass_len = strlen($password);
        if($name_len >= self::NAME_MIN && $name_len <= self::NAME_MAX && $pass_len >= self::PASSWORD_MIN && $pass_len <= self::PASSWORD_MAX &&
            $this->exist_name($name) === false
        ){
            return true;
        }
        return false;
    }

    function check_login($name, $password){
        $result = $this->con->fetch('SELECT COUNT(`id`), `id` FROM `host` WHERE `name` = ? AND `password` = ?', [$name, $this->hash($name, $password)]);
        if($result['COUNT(`id`)'] === '1'){
            return $result['id'];
        }
        return null;
    }

    function insert($name, $password){
        return $this->con->insert('host', ['name' => $name, 'password' => $this->hash($name, $password)], true);
    }

    function create_auto_login_key(){
        do{
            $key = random_str(32);
        }while($this->con->get_count('host', ['auto' => $this->hash_auto_login_key($key)]) > 0);
        return $key;
    }

    function update_auto_login_key($id, $key){
        $this->con->update('host', ['auto' => $this->hash_auto_login_key($key)], '`id` = ?', [$id]);
    }

    function check_auto_login($id, $key){
        $result = $this->con->fetchColumn('SELECT COUNT(`id`) FROM `host` WHERE `id` = ? AND `auto` = ?', [$id, $this->hash_auto_login_key($key)]);
        if($result === '1'){
            $new_key = $this->create_auto_login_key();
            $this->update_auto_login_key($id, $new_key);
            return $new_key;
        }
        return null;
    }

    function hash_auto_login_key($key){
        return sha256($key . self::$salt2);
    }

    function hash($name, $password){
        return sha256($password . self::$salt . $name);
    }

}
