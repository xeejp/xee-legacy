<?php

class ExplanationUI extends ModUIComponent{
    private $controller;
    private $pages=[], $no;
    private $variable_name;

    public function __construct ($controller, $id='') {
        $this->controller = $controller;
        $this->variable_name = 'ExpUI::no' . '_' . $id;
        $this->no = $this->controller->get_personal($this->variable_name, 0);
    }

    public function get_template_name ($name) {
        return 'ExplanationUI';
    }

    public function add_page ($title, $explanations) {
        $this->pages[] = ['title' => $title, 'explanations' => $explanations];
        return $this;
    }

    public function get_templates($name){
        $template = <<<'TMPL'
<h1>{title}</h1>
<div style="padding: 1em;">
 <div class="pure-u-1" style="border: solid 1px; margin: 0em;">
  <ul style="padding: 1em 2em 0em;">
{each explanations}
{if explanation}<li>{explanation}</li>
{elif explanation_sub}<li style="margin-left: 1em; font-size: small;">{explanation_sub}</li>
{elif explanation_blank}<br/>
{/if}
{/each}
  </ul>
 </div>
</div>
<div class="pure-u-1">
{if has_prev}<button id="{_name}-prev" class="pure-button" style="color: #fff; border-color: #1f8dd6; background-color: #1f8dd6; font-size: 125%; float: left;">前へ</a>{/if}
{if has_next}<button id="{_name}-next" class="pure-button" style="color: #fff; border-color: #1f8dd6; background-color: #1f8dd6; font-size: 125%; float: right;">次へ</a>{/if}
 <a style="clear: both;"></a>
</div>
TMPL;
        return [$this->get_template_name($name) => $template];
    }

    public function get_values($name){
        return [
            'title' => $this->pages[$this->no]['title'],
            'explanations' => $this->pages[$this->no]['explanations'],
            'has_prev' => isset($this->pages[$this->no-1]),
            'has_next' => isset($this->pages[$this->no+1]),
        ];
    }

    public function get_scripts($name){
        return [
            'value' => <<<'JS'
function (selector) {
    return ExplanationUI_get_page();
}
JS
            , 'event' => <<<'JS'
function (selector, update) {
    $(document)
        .on("click", "#" + selector + "-prev", ExplanationUI_shift_page.bind(null, -1, update))
        .on("click", "#" + selector + "-next", ExplanationUI_shift_page.bind(null,  1, update));
}
JS
            , 'other' => <<<JS
var ExplanationUI_page = {$this->no};
function ExplanationUI_get_page () {
    return ExplanationUI_page;
}
function ExplanationUI_shift_page (shift, update) {
    ExplanationUI_page += shift;
    update();
}
JS
        ];
    }

    public function input($name, $value){
        if (isset($this->pages[$value]))
            $this->no = $value;
        $this->controller->set_personal($this->variable_name, $this->no);
    }
}
