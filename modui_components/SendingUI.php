<?php

class SendingUI extends ModUIComponent{

    private $btn;
    private $sending;

    public function __construct($btn, $sending){
        $this->btn = $btn;
        $this->sending = $sending;
    }

    public function get_templates($name){
        $template = <<<TMPL
<span class="pure-form">
<input id="{_name}" type="text" value="">
<button class="pure-button pure-button-primary" id="{_name}-b">{$this->btn}</button>
</span>
TMPL;
        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name){
        return [];
    }

    public function get_scripts($name){
        return [
            'value' => 'function(selector){return $("#" + selector).val();}',
            'event' => <<<'JS'
function(selector, update){
    $(document).on("click", "#" + selector + "-b", update);
    $(document).on("keydown", "#" + selector, function(e){
        if (e.keyCode == 13) update();
    });
}
JS
        ];
    }

    public function input($name, $value){
        call_user_func($this->sending, $value);
    }

}
