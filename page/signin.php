<?php

if($_request->request_method === Request::GET){
    $_tmpl = new Template();
    //$tmpl->add_script();
    if($_host_session->is_login()){
        $_host_session->logout();
    }
    $token = get_token('signin');
    $_tmpl->add(<<<HTML
    <form method="POST" action="{$_(_URL)}signin">
    <input type="text" name="name">
    <input type="password" name="password">
    <input type="checkbox" name="remember" value="true">
    <button type="submit">Signin</button>
    <input type="hidden" name="{$_(_TOKEN)}" value="$token">
    </form>
HTML
);
    echo $_tmpl->display();
}else{
    if(!check_token('signin', $_request->get_string(_TOKEN)) &&
        ($name = $_request->get_string('name')) &&
        ($password = $_request->get_string('password')) &&
        ($id = $_host_model->check_login($name, $password)) !== null
    ){
        $_host_session->login($id);
        if($_request->get_string('remember', false) === 'true'){
            $_host_session->enable_auto_login();
        }
    }
    redirect_uri(_URL);
}
