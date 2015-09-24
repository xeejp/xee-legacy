<?php

if($_host_session->is_login !== true){
    redirect_uri(_URL . 'signin');
}

switch(explode('/', $_request->get_uri(0, 1))[0]){
case 'create':
    require DIR_ROOT . 'page/host/create_modui.php';
    break;
case 'management':
    require DIR_ROOT . 'page/host/management.php';
    break;
case 'admin':
    require DIR_ROOT . 'page/host/admin.php';
    break;
default:
    $_tmp_url = _URL;
    $_template = <<<TMPL
<div align="center" style="margin: auto; width: 320px;">
<h2>教師用ページ</h2>
<a class="pure-button" style="width: 320px;" href="{$_tmp_url}host/create">実験作成</a><br>
<a class="pure-button" style="width: 320px;" href="{$_tmp_url}host/management">管理画面</a><br>
<br/>
<a class="pure-button" href="{$_tmp_url}signout">サインアウト</a><br>
</div>
TMPL;
    $_tmpl = new Template();
    $_tmpl->lwte_add('host', $_template);
    $_tmpl->lwte_use('#container', 'host', []);
    echo $_tmpl->display();
    break;
}
