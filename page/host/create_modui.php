<?php

if (!defined('_CREATE_UI')) {
    $_vdb = new VarDB($_pdo, 'debug');
    define('_CREATE_UI', true);
    modui($_request, 'debug', $_vdb, './page/host/create_modui.php');
} else {
    $_con->add_component(new WebPageEdit($_con));
}
