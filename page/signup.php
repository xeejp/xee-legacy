<?php

if($_request->request_method === Request::GET){
    $_tmpl = new Template();
    //$tmpl->add_script();
    if($_host_session->is_login()){
        $_host_session->logout();
    }
    $_tmpl->lwte_add('signup', <<<HTML
<form method="POST" action="{$_(_URL)}signup">
<input type="text" name="name">
<input type="password" name="password">
<input type="password" name="password2">
<button type="submit">Signup</button>
<input type="hidden" name="{$_(_TOKEN)}" value="{token}">
</form>
{if error}
<p>
{switch error}
{case 0}不正なトークンです
{case 1}パスワードが間違っています
{case 2}無効なIDかパスワードです
{/switch}
</p>
{/if}
HTML
    );
    $_tmpl->lwte_use('#container', 'signup', ['token' => get_token('signup'), 'error' => $_request->get_int('error', null)]);
    echo $_tmpl->display();
}else{
    if(check_token('signup', $_request->get_string(_TOKEN))){
        redirect_uri(_URL . 'signup?error=0');
    }
    $_name = $_request->get_string('name');
    $_password = $_request->get_string('password');
    if($_password !== $_request->get_string('password2')){
        redirect_uri(_URL . 'signup?error=1');
    }
    if(!$_host_model->check_available($_name, $_password)){
        redirect_uri(_URL . 'signup?error=2');
    }
    $_result = $_host_model->insert($_name, $_password);
    $_host_session->login($_result);
    redirect_uri(_URL);
}
