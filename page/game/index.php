<?php
define('NEXT_DEFAULT', 5000);

$_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
$_modui = new ModUI('game', new NormalContainer());
$con = new Controller($_vdb, $_modui, 'default');
call_user_func(function($_con, $_require_dir){
    require $_require_dir;
}, $con, './game/' . $_game['directory'] . '/index.php');
if($_request->request_method === Request::GET){
    $page = $con->get_page();
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
            update_auto('game', data, lwte);
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
    $_tmpl->lwte_use('#container', 'game', $_result['values']);
    $_tmpl->add_script($_result['script']);
    $_tmpl->add_script(<<<JS
function update_modui(name, value){
    $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
    }).done(function(data){
        $("#container").html(lwte.useTemplate('game', data));
    });
}
current_page = "$page";
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
