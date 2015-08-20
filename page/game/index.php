<?php

$_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
$properties = [
    'game' = $_game,
    'experiment' = $_experiment,
    'participant' = $_participant
];
modui($_request, 'game', $_vdb, './game/' . $_game['directory'] . '/index.php', $properties);
