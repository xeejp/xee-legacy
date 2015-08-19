<?php

$_vdb = new VarDB($_pdo, 'debug');
modui($_request, 'debug', $_vdb, './debug.php');
