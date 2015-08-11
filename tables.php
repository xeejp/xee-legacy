<?php
$_tables = [
    'host' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'name' => 'VARCHAR(32) NOT NULL UNIQUE',
        'password' => 'VARCHAR(64) NOT NULL',
        'auto' => 'VARCHAR(64) NULL', //hashed by sha256
        ],
    'game' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'name' => 'VARCHAR(128) NOT NULL UNIQUE',
        'directory' => 'VARCHAR(32) NOT NULL'
        ],
    'experiment' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'host_id' => 'INT',
        'game_id' => 'INT',
        'password' => 'VARCHAR(64)', //hashed by sha256
        'status' => 'TINYINT'
        ],
    'participant' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'experiment_id' => 'INT',
        'last_access' => 'INT'
        ]
];

$_ddb = new DiffDB($_pdo);
foreach($_tables as $name => $structure){
    $_ddb->addTable($name, $structure);
}
$_ddb->updateDB();
