<?php

if($_host_session->is_login){
    $_experiment_id = $request->get_uri(0, 1);
    if($_experiment_id !== false){
        $_experiment = $_experiment_model->get($_experiment_id);
        if($_host_session->user_id !== $_experiment['host_id']){
            redirect_uri(_URL);
        }
        $_modui = new ModUI('test', new NormalContainer());
        require DIR_ROOT . 'game/' . $_game_id . '/admin.php';
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
            $result = $modui->display();

            $tmpl = new Template();
            foreach($result['templates'] as $key => $template){
                $tmpl->lwte_add($key, $template);
            }
            $tmpl->lwte_use('#container', 'test', $result['values']);
            $tmpl->add_script($result['script']);
            $tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
console.log(lwte);
console.log(data);
        $("#container").html(lwte.useTemplate("test", data));
    });
}
JS
        );
            echo $tmpl->display();
        }else{
            $modui->input($_POST);
        }
    }
}else{
    redirect_uri(_URL);
}
