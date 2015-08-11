<?php

if($_request->request_method === Request::GET){
    $_ted = new TEDiff($_pdo);
    $_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
}else{
}
