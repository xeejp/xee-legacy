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
        ($experiment_id = $_request->get_string('experiment_id')) &&
        ($name = $_request->get_string('name')) &&
        ($id = $_participant_model->check_login($experiment_id, $name)) !== null
    ){
        $_participant_session->login($id);
    }
    redirect_uri(_URL . 'game');
}
