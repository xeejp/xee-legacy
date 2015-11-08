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
{each list}{description}: <input id="{_name}-{id} type="text" value=""><br/>{/each}
<button id="{_name}-button">{button_title}</button><br/>
TMPL;

        dump('[MultiSendingUI get_templates] template: ' . dump($template), true);

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['list' => $this->list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name){
        $value = 'function(selector) { return { ';
        foreach ( $this->list as $line ) {
            $id = $line['id'];
            $value .= $id . ': $(\'#\' + selector + \'-' . $id . '\').val(), ';
        }
        $value = rtrim($value, ", ");
        $value .= '}; }';

        $event = <<<'JS'
function(selector, update) {
    $(document).on("click", "#" + selector + "-button", update);
}
JS;

        dump('[MultiSendingUI get_scripts] value: ' . dump($value), true);
        dump('[MultiSendingUI get_scripts] event: ' . dump($event), true);

        return ['value' => $value, 'event' => $event];
    }

    public function input($name, $value){
        dump('[MultiSendingUI input] name: ' . dump($name), true);
        dump('[MultiSendingUI input] value: ' . dump($value), true);

        call_user_func($this->sending, $value);
    }

}

