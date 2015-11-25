<?php

class MatchingButton extends ModUIComponent{

    private $con;
    private $func_can_matching;
    private $func_matching;

    public function __construct($con, $func_can_matching, $func_matching){
        $this->con = $con;
        $this->func_can_matching = $func_can_matching;
        $this->func_matching = $func_matching;
    }

    public function get_templates($name){
        return [$this->get_template_name($name) => <<<TMPL
<button id="{_name}">マッチング{if can_match}する{else}できません{/if}</button>
TMPL
        ];
    }

    public function get_values($name){
        return ['can_match' => call_user_func($this->func_can_matching, $this->con)];
    }

    public function get_scripts($name){
        return ['event' => <<<JS
function(selector, update){
    $(document).on("click", "#" + selector, update);
}
JS
        ];
    }

    public function input($name, $value){
        if(call_user_func($this->func_can_matching, $this->con)){
            foreach(call_user_func($this->func_matching, $this->con) as $id => $list){
                foreach($list as $key => $value){
                    $this->con->set_personal($key, $value, $id);
                }
            }
        }
    }
}
