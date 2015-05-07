<?php

$_ddb = new DiffDB($_pdo);
$_ddb->addTable('host', [
    'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
    'name' => 'VARCHAR(32) NOT NULL UNIQUE',
    'password' => 'VARCHAR(64) NOT NULL',
    'auto' => 'VARCHAR(64) NULL'
]);
$_ddb->updateDB();
