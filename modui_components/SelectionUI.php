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
{description}: 
<select id="{_name}-selection-{id}">
{each options}<option value="{value}">{text}</option>{/each}
</select><br/>
{/each} 
<button id="{_name}-button">{button_title}</button>
TMPL;

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        return ['list' => $this->list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name) {
        /*
        $value = 'function(selector) { return { ';
        foreach ( $this->list as $list ) {
            $suffix = $list['id'];
            $value .= '\'' . $suffix . '\': $(\'#\' + selector + \'-selection-' . $suffix . '\').val(), ';
        }
        $value = rtrim($value, ', ');
        $value .= '}; }';
         */
        $value = <<<'JS'
function(selector) {
    return {
        'test01': $("#" + selector + "-selection-test01").val(),
        'test02': $("#" + selector + "-selection-test02").val()
    };
}
JS;

        $event = <<<'JS'
function(selector, update) { 
    $(document).on("click", "#" + selector + "-button", update);
}
JS;

        dump('value: ' . dump($value), true);
        dump('event: ' . dump($event), true);

        return [
            'value' => $value, 'event' => $event
        ];
    }

    public function input($name, $value) {
        dump('debug_backtrace: ' . serialize(debug_backtrace()), true);

        dump('click_name:' . dump($name), true);
        dump('click_input: ' . dump($value), true);
        call_user_func($this->sending, $value);
    }

}

