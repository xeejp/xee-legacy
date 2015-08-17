<?php

if($_host_session->is_login){
    $_experiment_id = $request->get_uri(0, 1);
    if($_experiment_id !== false){
        $_experiment = $_experiment_model->get($_experiment_id);
        if($_host_session->user_id !== $_experiment['host_id']){
            redirect_uri(_URL);
        }
        $_tmpl = new Template();
        $_tmpl->add('<script src="' . _URL . 'js/admin.js' . '"></script>');
        $_tmpl->display();
    }
}else{
    redirect_uri(_URL);
}
