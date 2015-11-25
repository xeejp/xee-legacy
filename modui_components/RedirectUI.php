<?php

class RedirectUI extends ModUIComponent{
    private $url, $trigger;

    public function __construct($url, $trigger){
        $this->url = $url;
        $this->trigger = $trigger;
    }
    public function get_scripts($name){
        if ($this->trigger)
            return ['other' => "location.href='{$this->url}';\n"];
        else
            return [];
    }
}
