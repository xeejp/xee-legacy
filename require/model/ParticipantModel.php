<?php

class ParticipantModel extends Model{

    const NAME_MIN = 1, NAME_MAX = 32, PASSWORD_MIN = 4, PASSWORD_MAX = 64;

    function exist_id($experiment_id, $id){
        if($this->con->get_count('participant', ['experiment_id' => $experiment_id, 'id' => $id]) === 1){
            return true;
        }
        return false;
    }

    function insert($experiment_id, $name){
        return $this->con->insert('participant', ['experiment_id' => $experiment_id, 'name' => $name], true);
    }

    function get_all($experiment_id){
        return $this->con->fetchAll('SELECT `id`, `name`, `experiment_id`, `last_access` FROM `participant` WHERE `experiment_id` = ?', $experiment_id);
    }

    function get($id){
        return $this->con->fetch('SELECT `id`, `name`, `experiment_id`, `last_access` FROM `participant` WHERE `id` = ?', $id);
    }

    function get_by_name($experiment_id, $name){
        return $this->con->fetch('SELECT `id`, `name`, `experiment_id`, `last_access` FROM `participant` WHERE `experiment_id` = ? AND `name` = ?', $experiment_id, $name);
    }

    function check_name($experiment_id, $name){
        $result = $this->con->fetch('SELECT COUNT(`id`), `id` FROM `participant` WHERE `experiment_id` = ? AND `name` = ?', [$experiment_id, $name]);
        if($result['COUNT(`id`)'] === '1'){
            return $result['id'];
        }
        return null;
    }

    function check_login($experiment_id, $name){
        if(($id = $this->check_name($experiment_id, $name)) !== null){
            return $id;
        }
        $id = $this->con->insert('participant', ['experiment_id' => $experiment_id, 'name' => $name], true);
        return $id;
    }

}
