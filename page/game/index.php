<?php

$_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
$properties = [
    'game' => $_game,
    'experiment' => $_experiment,
    'participant' => $_participant,
    'participants' => $_participant_model->get_all($_experiment['id'])
];
modui($_request, 'game', $_vdb, './game/' . $_game['directory'] . '/index.php', 5000, $properties);
