<?php

class DataController {
    private $_con;
    private $data, $is_lock;
    private $temp_values = [];

    public $participant, $participants, $experiment, $game;

    public function __construct ($_con, $name) {
        $this->_con = $_con;
        $this->data = new DataIO($_con, 'con_'. $name);
        $this->is_lock = false;
        if (!is_array($this->data->get()))
            $this->data->set([]);
        $this->set_properties($_con);
    }

    public static function replace_con (&$_con, $name) {
        $con = $_con;
        $_con = new DataController($con, $name);
    }

    public function lock () {
        $this->data->lock();
        $this->is_lock = true;
        $this->temp_values = $this->data->get();
    }
    public function unlock () {
        $this->data->set($this->temp_values);
        $this->is_lock = false;
        $this->data->unlock();
    }

    public function set ($name, $value) {
        if (!$this->is_lock) {
            $this->data->lock();
            $values = $this->data->get();
            $values[$name] = $value;
            $this->data->set($values);
            $this->data->unlock();
        } else {
            $this->temp_values[$name] = $value;
        }
    }
    public function get ($name, $default_value) {
        if (!$this->is_lock) {
            $values = $this->data->get();
            return isset($values[$name])? $values[$name]: $default_value;
        } else {
            return isset($this->temp_values[$name])? $this->temp_values[$name]: $default_value;
        }
    }
    public function set_personal ($name, $value, $participant_id=null) {
        if (is_null($participant_id))
            $participant_id = $this->participant['id'];
        $this->set('_participant_'. $participant_id .'_'. $name, $value);
    }
    public function get_personal ($name, $default_value, $participant_id=null) {
        if (is_null($participant_id))
            $participant_id = $this->participant['id'];
        return $this->get('_participant_'. $participant_id .'_'. $name, $default_value);
    }

    public function add_component ($component, $hook=null) {
        $this->_con->add_component($component, $hook);
    }

    private function set_properties ($_con) {
        if (isset($_con->participant))
            $this->participant = $_con->participant;
        if (isset($_con->participants))
            $this->participants = $_con->participants;
        if (isset($_con->experiment))
            $this->experiment = $_con->experiment;
        if (isset($_con->game))
            $this->game = $_con->game;
    }
}
