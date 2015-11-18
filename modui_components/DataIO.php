<?php

class DataIO {
    private $name, $handle, $is_lock;

    public function __construct ($con, $name) {
        $this->name = DIR_ROOT .'game/'. $con->game['directory'] .'/'. array_pop(explode('/', $name)) .'.json';
        $this->handle = fopen($this->name, 'cb+');
        $this->is_lock = false;
    }
    public function __destruct () {
        if ($this->is_lock)
            $this->unlock();
        fclose($this->handle);
    }

    public function get () {
        return json_decode($this->read(), true);
    }
    public function set ($data) {
        if (is_resource($data))
            throw new Exception('Cannot set data of the Resource type');
        $this->write(json_encode($data));
    }
    public function lock () {
        flock($this->handle, LOCK_SH);
        $this->is_lock = true;
    }
    public function unlock () {
        flock($this->handle, LOCK_UN);
        $this->is_lock = false;
    }

    private function read () {
        if (!$this->is_lock)
            flock($this->handle, LOCK_SH);

        $data = stream_get_contents($this->handle, -1, 0);

        if (!$this->is_lock)
            flock($this->handle, LOCK_UN);
        return $data;
    }
    private function write ($data) {
        flock($this->handle, LOCK_EX);

        ftruncate($this->handle, 0);
        rewind($this->handle);
        fwrite($this->handle, $data);
        fflush($this->handle);

        if (!$this->is_lock)
            flock($this->handle, LOCK_UN);
        else
            flock($this->handle, LOCK_SH);
    }
}
