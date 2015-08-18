<?php
define('NEXT_DEFAULT', 5000);

$_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
//$_page = $_vdb->get('_page');
$_page = 'a';
$_modui = new ModUI('modbom', new NormalContainer());
require DIR_ROOT . 'game/' . $_game['directory'] . '/index.php';
if($_request->request_method === Request::GET){
    $_modui->enable_auto_reload(5000, <<<JS
function(){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { _page: current_page }
    }).done(function(data){
        if(data == 'reload'){
            location.reload(true);
        }else{
            update_auto('modbom', data, lwte);
        }
    });
}
JS
    );
    $_result = $_modui->display();
    $_tmpl = new Template();
    foreach($_result['templates'] as $key => $template){
        $_tmpl->lwte_add($key, $template);
    }
    $_tmpl->lwte_use('#container', 'modbom', $_result['values']);
    $_tmpl->add_script($_result['script']);
    $_tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        $("#container").html(lwte.useTemplate('modbom', data));
    });
}
current_page = "$_page";
JS
    );
    echo $_tmpl->display();
}else{
    if(isset($_POST['_page']) && $_POST['_page'] !== $_page){
        header('Content-Type: application/json');
        echo json_encode('reload');
        exit();
    }
    $_modui->input($_POST);
}
