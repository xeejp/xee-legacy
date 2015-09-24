<?php

$_vdb = new VarDB($_pdo, 'debug');
$con = new Controller($_vdb, $_modui, 'default');
call_user_func(function($_con, $_require_dir){
    require $_require_dir;
}, $con, './debug.php');
if($_request->request_method === Request::GET){
    $_modui->enable_auto_reload(5000, <<<JS
function(){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
    }).done(function(data){
        update_auto('debug', data, lwte);
    });
}
JS
);
    $_result = $_modui->display();

    $_tmpl = new Template();
    foreach($_result['templates'] as $key => $template){
        $_tmpl->lwte_add($key, $template);
    }
    $_tmpl->lwte_use('#container', 'debug', $_result['values']);
    $_tmpl->add_script($_result['script']);
    $_tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        update_auto('debug', data, lwte);
    });
}
JS
);
    echo $_tmpl->display();
}else{
    dump($_POST,true);
    $_modui->input($_POST);
}
