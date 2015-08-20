<?php

function modui($_request, $name, $_vdb, $require_dir, $auto_time=5000, $properties=[]){
    $_modui = new ModUI($name, new NormalContainer());
    $con = new Controller($_vdb, $_modui);
    foreach($properties as $key => $value){
        $con->{$key} = $value;
    }
    call_user_func(function($_con, $_require_dir){
        require $_require_dir;
    }, $con, $require_dir);
    if($_request->request_method === Request::GET){
        $_result = $_modui->display(<<<JS
function(name, value, update){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        update(data);
    });
}
JS
        , <<<JS
function(update){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
    }).done(function(data){
        update(data);
    });
}
JS
        , $auto_time);
        $_tmpl = new Template();
        foreach($_result['templates'] as $key => $template){
            $_tmpl->lwte_add($key, $template);
        }
        $_tmpl->lwte_use('#container', $name, $_result['values']);
        $_tmpl->add_script($_result['script']);
        echo $_tmpl->display();
    }else{
        $_modui->input($_POST);
    }
}
