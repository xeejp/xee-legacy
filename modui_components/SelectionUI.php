<?php

class SelectionUI extends ModUIComponent {
    private $button_title, $list, $sending;


    public function __construct($button_title, $list, $sending){
        $this->button_title = $button_title;
        $this->list         = $list;
        $this->sending      = $sending;
    }

    public function get_templates($name){
        $template = <<<TMPL
<form>
{each list}
<select>
{description}{each menu}<option value='{value}'>{text}</option>{/each}
</select>
{/each}
</form>
TMPL;

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name){
        return [];
    }

    public function get_scripts($name){
        return ['value' => 'function(selector){return $("#" + selector).val();}', 'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-b", update);}'];
    }

    public function input($name, $value){
        call_user_func($this->sending, $value);
    }

}
