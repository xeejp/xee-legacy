<?php

$_tmp_url = _URL;
$_tmpl = new Template();
$_tmpl->add_script(<<<SCRIPT
</script>
<link rel="stylesheet" type="text/css" href="{$_tmp_url}template/WebPageEdit/popup_design.css" />
<link rel="stylesheet" type="text/css" href="{$_tmp_url}template/WebPageEdit/design.css"/>
<link rel="stylesheet" type="text/css" href="{$_tmp_url}template/WebPageEdit/CLEditor/jquery.cleditor.css" />
<script type="text/javascript" src="{$_tmp_url}template/WebPageEdit/CLEditor/jquery.cleditor.min.js"></script>
<script type="text/javascript" src="{$_tmp_url}template/WebPageEdit/function.js"></script>
<script>

SCRIPT
);
$_tmpl->lwte_add('host/create', Template::load_template('template/WebPageEdit/io_edit.php'));
$_tmpl->lwte_use('#container', 'host/create', ['token_name' => _TOKEN, 'token' => get_token('host/create')]);
echo $_tmpl->display();

