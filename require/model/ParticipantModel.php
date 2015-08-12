<?php

class ParticipantModel{

    const NAME_MIN = 1, NAME_MAX = 32, PASSWORD_MIN = 4, PASSWORD_MAX = 64;
    const S_RESERVED = '0', S_RUNNIG = '1', S_FINISHED = '2', S_REMOVED = '3';

    function exist_id($experiment_id, $id){
        if($this->con->get_count('participant', ['experiment_id' => $experiment_id, 'id' => $id]) === 1){
            return true;
        }
        return false;
    }

    function exist_id($experiment_id, $id){
        if($this->con->get_count('participant', ['experiment_id' => $experiment_id, 'id' => $id]) === 1){
            return true;
        }
        return false;
    }

    function insert($experiment_id, $id){
        return $this->con->insert('participant', ['experiment_id' => $experiment_id, 'id' => $id], true);
    }

    function get_all($experiment_id){
        return $this->con->fetchAll('SELECT `id`, `experiment_id`, `last_access` FROM `participant` WHERE `experiment_id` = ?', $experiment_id);
    }

    function get($experiment_id, $id){
        return $this->con->fetch('SELECT `id`, `experiment_id`, `last_access` FROM `participant` WHERE `experiment_id` = ? AND `id` = ?', $experiment_id, $id);
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
