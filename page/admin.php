<?php

if($_host_session->is_login){
    $_experiment_id = $_request->get_uri(0, 1);
    if($_experiment_id !== false){
        $_experiment = $_experiment_model->get($_experiment_id);
        if($_host_session->user_id !== $_experiment['host_id']){
            redirect_uri(_URL);
        }
        $_modui = new ModUI('admin', new NormalContainer());
        $_game = $_game_model->get($_experiment['game_id']);
        $con = new Controller($_vdb, $_modui, 'default');
        call_user_func(function($_con, $_require_dir){
            require $_require_dir;
        }, $con, './game/' . $_game['directory'] . '/admin.php');
        if($_request->request_method === Request::GET){
            $_modui->enable_auto_reload(5000, <<<JS
function(){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
    }).done(function(data){
        update_auto('admin', data, lwte);
    });
}
JS
        );
            $_result = $_modui->display();

            $_tmpl = new Template();
            foreach($_result['templates'] as $key => $template){
                $_tmpl->lwte_add($key, $template);
            }
            $_tmpl->lwte_use('#container', 'admin', $_result['values']);
            $_tmpl->add_script($_result['script']);
            $_tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        update_auto('admin', data, lwte);
    });
}
JS
        );
            echo $_tmpl->display();
        }else{
            $_modui->input($_POST);
        }
    }
}else{
    redirect_uri(_URL);
}
