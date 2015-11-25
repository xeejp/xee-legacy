<?php

class ParticipantsList extends ModUIComponent{

    private $con;

    public function __construct($con){
        $this->con = $con;
    }

    public function get_templates($name){
        return [$this->get_template_name($name) => <<<TMPL
<p>人数：{count}</p>
{each participants}
<p>id: {id}, name: {name}</p>
{/each}

TMPL
        ];
    }

    public function get_values($name){
        return [
            'count' => count($this->con->participants),
            'participants' => $this->con->participants
        ];
    }

}
