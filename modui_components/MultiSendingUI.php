<?php

class MultiSendingUI extends ModUIComponent {

    private $button_title, $list, $sending;


    public function __construct($button_title, $list, $sending) {
        $this->button_title = $button_title;
        $this->list         = $list;
        $this->sending      = $sending;
    }

    public function get_templates($name) {
        $list = call_user_func($this->list);
        $template = "";
        foreach ( $list as $line ) {
            $id = $line['id'];
            $desc = $line['description'];
            $template .= $desc . ': <input id="{_name}-' . $id . '" type="text"><br/>';
        }
        $template .= '<button id="{_name}-button">{button_title}</button><br/>';

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['button_title' => $this->button_title];
    }

    public function get_scripts($name){
        $list = call_user_func($this->list);
        $value = 'function(selector) { return { ';
        foreach ( $list as $line ) {
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

        return ['value' => $value, 'event' => $event];
    }

    public function input($name, $value){
        call_user_func($this->sending, $value);
    }

}

