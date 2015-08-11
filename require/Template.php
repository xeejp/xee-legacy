<?php

class Template{

    function __construct(){
        $this->site_name = 'Xee';
        $this->title = '';
        $this->content = [];
        $this->scripts = [];
        $this->description = <<<'DESC'
DESC;
        $this->navbar = '';
    }

    function add($text){
        $this->content[] = $text;
    }

    function add_script($script){
        $this->scripts[] = $script;
    }

    function lwte_add($name, $template){
        $template = str_replace('"', '\\"', str_replace("\n", "\\n", $template));
        $this->add_script(<<<JS
lwte.addTemplate("$name", "$template");
JS
    );
    }

    function lwte_use($selector, $name, $data){
        $data = json_encode($data, true);
        $this->add_script(<<<JS
$("$selector").html(lwte.useTemplate("$name", $data));
JS
    );
    }

    function display(){
        global $_;
        if(strlen($this->title) !== 0){
            $this->title .= ' - ';
        }
        $script = implode('', $this->scripts);
        $display[] = <<<HEAD
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{$this->title}{$this->site_name}</title>
<meta charset="utf-8">
<meta name="description" content="{$this->description}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="{$_(_URL)}js/jquery-2.1.3.min.js"></script>
<script src="{$_(_URL)}js/lwte/lwte.js"></script>
<script> lwte = new LWTE();</script>
</head>
<body>
<div id="container">
HEAD;
        $display[] = implode('', $this->content);
        $display[] = <<<FOOT
</div>
<script>
$script
</script>
</body>
</html>
FOOT;
        return implode('', $display);
    }

}
