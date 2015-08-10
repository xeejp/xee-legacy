<?php

$con = new EasySql('mysql:dbname=xee;host=localhost', 'root', '');
$con->debug(true);
$vdb = new VarDB($con, 'a');
$vdb->setup();
