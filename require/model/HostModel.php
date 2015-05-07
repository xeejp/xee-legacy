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
        $result = $this->con->fetch('SELECT COUNT(`id`), `id` FROM `host` WHERE `name` = ? AND `password` = ?', [$name, sha256($password . self::$salt)]);
        if($result['COUNT(`id`)'] === '1'){
            return $result['id'];
        }
        return null;
    }

    function insert($name, $password){
        return $this->con->insert('host', ['name' => $name, 'password' => sha256($password . self::$salt)], true);
    }

    function create_auto_login_key(){
        do{
            $key = random_str(32);
        }while($this->con->get_count('host', ['auto' => $key]) > 0);
        return $key;
    }

    function check_auto_login($key){
        $result = $this->con->fetch('SELECT COUNT(`id`), `id` FROM `host` WHERE `auto` = ?', sha256($key . self::$salt2));
        if($result['COUNT(`id`)'] === '1'){
            $new_key = $this->create_auto_login_key();
            $this->con->update('host', ['auto' => sha256($new_key . self::$salt2)], '`id` = ?', $result['id']);
            return [$result['id'], $new_key];
        }
        return null;
    }

}
