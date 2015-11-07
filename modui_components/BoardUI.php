<?php

class BoardUI extends ModUIComponent {
    private $controller, $title, $super_user;
    private $data;

    public function __construct($controller, $title, $super_user=false){
        $this->controller = $controller;
        $this->title = $title;
        $this->data = new DataContainer($controller, '_b'. $title);
        $this->super_user = $super_user;
    }

    public function get_templates($name){
        return [$this->get_template_name($name) => <<<TMPL
<div style="background-color:#efefef;text-color:black;">
<hr style="background-color:#888;color:#888;border-width:0;height:1px;position:relative;top:-.4em;" />
<h1 style="color:red;font-size:larger;font-weight:normal;margin:-.5em 0 0;">{$this->title}</h1>
<div><dl class="thread" style="margin-right:185px;word-wrap:break-word; ">
{if responses}{each responses}
<dt>{no} ：
{if mail}<a href="mailto:{mail}"><b>{name}</b></a>
{else}{name}
{/if}：{date} ID:{id}</dt>
<dd>{text}</dd> <br><br>
{/each}{/if}
</dl>
</div>
<hr/>
<button id="{_name}-button">書き込む</button>
名前： <input id="{_name}-name" size=19>
E-mail: <input id="{_name}-mail" size=19 disabled><br>
<textarea id="{_name}-text" rows=5 cols=70 wrap=off></textarea>
</div>
TMPL
        ];
    }

    public function get_values($name){
        $defaut_time_zone = date_default_timezone_get();
        date_default_timezone_set('Asia/Tokyo');
        $week = ['日', '月', '火', '水', '木', '金', '土'];
        $responses = [];
        foreach($this->data as $response) {
            list($timestamp, $millis) = explode('.', $response['date']);
            if ($this->super_user)
                $response['id'] .= '('. $response['user'] .')';
            $responses[] = [
                'no' => $response['no']+1,
                'name' => $response['name'],
                'mail' => ($response['mail'] != '')? $response['mail']: false,
                'text' => $response['text'],
                'date' => date('Y/m/d', $timestamp) .'('. $week[date('w', $timestamp)] .')'. date('H:i:s', $timestamp) .'.'. substr($millis, 0, 2),
                'id' => $response['id']
            ];
        }
        date_default_timezone_set($defaut_time_zone);
        return ['responses_num' => count($responses), 'responses' => $responses];
    }

    public function get_scripts($name){
        return [
            'value' => <<<'JS'
function(selector){
    return {
        name: $('#' + selector + '-name').val(),
        mail: $('#' + selector + '-mail').val(),
        text: $('#' + selector + '-text').val()
    };
}
JS
            , 'event' => <<<'JS'
function(selector, update){
    $(document).on("click", "#" + selector + "-button", update);
}
JS
        ];
    }

    public function input($name, $value){
        if (!isset($value['name'], $value['mail'], $value['text']))
            return;
        if ($value['text'] == '')
            return;
        if ($value['name'] == '')
            $value['name'] = '名無しのどん兵衛';
        list($micro, $timestamp) = explode(' ', microtime());
        $data = &$this->data->get_ref();
        $this->data->set($no = count($data), [
            'no' => $no,
            'name' => htmlspecialchars($value['name'], ENT_QUOTES),
            'mail' => htmlspecialchars($value['mail'], ENT_QUOTES),
            'text' => htmlspecialchars($value['text'], ENT_QUOTES),
            'date' => $timestamp .'.'. substr(explode('.', $micro)[1], 0, 3),
            'id' => (!$this->super_user)?
                substr(sha1($this->controller->participant['id']), 0, 8):
                '00000000',
            'user' => $this->controller->participant['name']
        ]);
    }
}
