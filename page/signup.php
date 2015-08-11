<?php

if($_request->request_method === Request::GET){
    $_tmpl = new Template();
    //$tmpl->add_script();
    if($_host_session->is_login()){
        $_host_session->logout();
    }
    $token = get_token('signup');
    $_tmpl->add(<<<HTML
    <form method="POST" action="{$_(_URL)}signup">
    <input type="text" name="name">
    <input type="password" name="password">
    <input type="password" name="password2">
    <button type="submit">Signup</button>
    <input type="hidden" name="{$_(_TOKEN)}" value="$token">
    </form>
HTML
    );
    echo $_tmpl->display();
}else{
    if(check_token('signup', $_request->get_string(_TOKEN)) &&
        ($name = $_request->get_string('name')) &&
        ($password = $_request->get_string('password')) &&
        ($password === $_request->get_string('password2')) &&
        ($_host_model->check_available($name, $password))
    ){
        $result = $_host_model->insert($name, $password);
        $_host_session->login($result);
    }
    redirect_uri(_URL);
}
