<?php

class SelectionUI extends ModUIComponent {
    private $button_title, $list, $sending;


    public function __construct($button_title, $list, $sending) {
        $this->button_title = $button_title;
        $this->list         = $list;
        $this->sending      = $sending;
    }

    public function get_templates($name) {
        $template = <<<TMPL
{each list}
<select id="{_name}-selection-{id}">
{description}{each menu}<option value="{value}">{text}</option>{/each}
</select>
{/each} 
<button id="{_name}-button">{button_title}</button>
TMPL;

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['list' => $this->list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name) {
        $value = 'function(selector) { return { ';
        foreach ( $this->list as $list ) {
            $suffix = $list['id'];
            $value .= $suffix . ': $("#" + selecor + \'-selection-' . $suffix . '\').val(), ';
        }
        $value .= '}; }';

        $event = <<<'JS'
function(selector, update) { 
    $(document).on("click", "#" + selector "-button", update);
}
JS;

        return [
            'value' => $value, 'event' => $event
        ];
    }

    public function input($name, $value) {
        call_user_func($this->sending, $value);
    }

}

