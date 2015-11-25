<?php
if($_request->request_method === Request::GET){
    if($_participant_session->is_login()){
        $_participant_session->logout();
    }
    $template = Template::load_template('template/index.php');
    $_tmpl = new Template();
    $_tmpl->lwte_add('top', $template);
    $_tmpl->lwte_use('#container', 'top', ['token_name' => _TOKEN, 'TOKEN' => get_token('top')]);
    echo $_tmpl->display();
}else{
    if(!check_token('top', $_request->get_string(_TOKEN)) &&
            ($_password = $_request->get_string('password')) &&
            (($_experiment = $_experiment_model->get_by_password($_password)) != null) &&
            ($_name = $_request->get_string('name')) &&
            (($_id = $_participant_model->check_login($_experiment['id'], $_name)) != null)){
        $_participant_session->login($_id);
        redirect_uri(_URL . 'game');
    }
    redirect_uri(_URL);
}
