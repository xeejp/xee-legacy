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
<fieldset id={_name} class="pure-form">
{each list}{description}: <input name="{id}" type="text" value="{dvalue}"><br/>{/each}
<button class="pure-button" id="{_name}-button">{button_title}</button><br/>
</fieldset>
TMPL;

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['list' => $this->list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name){
        $value = <<<'JS'
function(selector) {
    var list = document.getElementById(selector);
    var values = {};
    for ( var i = 0; i < list.childNodes.length; i++ ) {
        if ( list.childNodes[i].type == "text" ) {
            var name = list.childNodes[i].name;
            var value = list.childNodes[i].value;
            values[name] = value;
        }
    }

    return values;
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
        call_user_func($this->sending, $value);
    }

}

