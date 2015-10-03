<?php

if($_request->request_method === Request::GET){
    $_tmpl = new Template();
    //$tmpl->add_script();
    if($_host_session->is_login()){
        $_host_session->logout();
    }
    $_tmpl->lwte_add('signin', <<<HTML
<div style="margin: auto; width: 320px">
<form class="pure-form pure-form-stacked" method="POST" action="{$_(_URL)}signin">
<fieldset>
<input type="text" name="name" style="width: 100%" placeholder="ID">
<input type="password" name="password" style="width: 100%" placeholder="password">
<lable class="pure-checkbox" style="width: 100%"><input type="checkbox" name="remember" value="true">Remember Me</lable>
<button class="pure-button pure-button-primary" type="submit" style="width: 100%">Signin</button>
<input type="hidden" name="{$_(_TOKEN)}" value="{token}">
</fieldset>
</form>
{if error}
<p>
{switch error}
{case 0}不正なトークンです
{case 1}IDまたはパスワードが間違っています
{/switch}
</p>
{/if}
</div>
HTML
);
    $_tmpl->lwte_use('#container', 'signin', ['token' => get_token('signin'), 'error' => $_request->get_int('error', null)]);
    echo $_tmpl->display();
}else{
    if(check_token('signin', $_request->get_string(_TOKEN))){
        redirect_uri(_URL . 'signin?error=0');
    }
    $name = $_request->get_string('name');
    $password = $_request->get_string('password');
    if(($id = $_host_model->check_login($name, $password)) == null){
        redirect_uri(_URL . 'signin?error=1');
    }
    $_host_session->login($id);
    if($_request->get_string('remember', false) === 'true'){
        $_host_session->enable_auto_login();
    }
    redirect_uri(_URL . 'host');
}
