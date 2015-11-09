<?php

class MultiSendingUI extends ModUIComponent {

    private $button_title, $list, $sending;


    public function __construct($button_title, $list, $sending) {
        $this->button_title = $button_title;
        $this->list         = $list;
        $this->sending      = $sending;
    }

    public function get_templates($name) {
        $template = <<<TMPL
<form id="{_name}">
{each list}{description}: <input name="{id}" type="text"><br/>{/each}
<button id="{_name}-button">{button_title}</button><br/>
</form>
TMPL;

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['list' => $this->list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name){
        $value = <<<'JS'
function(selector) {
    var $form = $('#' + selector);
    var arrVal = $form.serializeArray(); 

    return arrVal;
}
JS;

        $event = <<<'JS'
function(selector, update) {
    $(document).on("click", "#" + selector + "-button", update);
}
JS;

        return ['value' => $value, 'event' => $event];
    }

    public function input($name, $value){
        $list = [];
        foreach ( $value as $line ) {
            $list[strval($line['name'])] = strval($line['value']);
        }
        call_user_func($this->sending, $list);
    }

}

