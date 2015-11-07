<?php

class SelectionUI extends ModUIComponent {
    private $button_title, $list, $sending;


    public function __construct($button_title, $list, $sending) {
        $this->button_title = $button_title;
        $this->list         = $list;
        $this->sending      = $sending;

        // dump('button_title: ' . dump($this->button_title), true);
        // dump('list: ' . dump($this->list), true);
        // dump('sending: ' . dump($this->sending), true);
    }

    public function get_templates($name) {
        $template = <<<TMPL
{each list}
{description}: 
<select id="{_name}-selection-{id}">
{html options}
</select><br/>
{/each} 
<button id="{_name}-button">{button_title}</button>
TMPL;

        dump('template: ' . dump($template), true);

        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name) {
        $list = [];
        foreach ( $this->list as $line ) {
            $id = $line['id'];
            $description = $line['description'];
            $options = "";
            foreach ( $line['options'] as $option ) {
                $value = $option['value'];
                $text = $option['text'];
                $options .= '<option value="' . $value . '">' . 
                            $text . '</option>';
            }
            $list[] = ['id' => $id, 'description' => $description, 'options' => $options];
        }

        dump('get_values: ' . dump($list), true);
        return ['list' => $list, 'button_title' => $this->button_title];
    }

    public function get_scripts($name) {
        $value = 'function(selector) { return { ';
        foreach ( $this->list as $list ) {
            $suffix = $list['id'];
            $value .= '\'' . $suffix . '\': $(\'#\' + selector + \'-selection-' . $suffix . '\').val(), ';
        }
        $value = rtrim($value, ', ');
        $value .= '}; }';

        $event = <<<'JS'
function(selector, update) { 
    $(document).on("click", '#' + selector + '-button', update);
}
JS;

        dump('value: ' . dump($value), true);
        dump('event: ' . dump($event), true);

        return [
            'value' => $value, 'event' => $event
        ];
    }

    public function input($name, $value) {
        dump('click_name:' . dump($name), true);
        dump('click_input: ' . dump($value), true);
        call_user_func($this->sending, $value);
    }

}

