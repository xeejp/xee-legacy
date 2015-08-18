<?php

if($_host_session->is_login){
    $_experiment_id = $_request->get_uri(0, 1);
    if($_experiment_id !== false){
        $_experiment = $_experiment_model->get($_experiment_id);
        if($_host_session->user_id !== $_experiment['host_id']){
            redirect_uri(_URL);
        }
        $_modui = new ModUI('test', new NormalContainer());
        $_game = $_game_model->get($_experiment['game_id']);
        require DIR_ROOT . 'game/' . $_game['directory'] . '/admin.php';
        if($_request->request_method === Request::GET){
            $_modui->enable_auto_reload(5000, <<<JS
function(){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
    }).done(function(data){
        update_auto('test', data, lwte);
    });
}
JS
        );
            $_result = $_modui->display();

            $_tmpl = new Template();
            foreach($_result['templates'] as $key => $template){
                $_tmpl->lwte_add($key, $template);
            }
            $_tmpl->lwte_use('#container', 'test', $_result['values']);
            $_tmpl->add_script($_result['script']);
            $_tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        $("#container").html(lwte.useTemplate("test", data));
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
