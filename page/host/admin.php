<?php

if($_host_session->is_login){
    $_experiment_id = $_request->get_uri(0, 1);
    if($_experiment_id !== false){
        $_experiment = $_experiment_model->get($_experiment_id);
        if($_host_session->user_id !== $_experiment['host_id']){
            redirect_uri(_URL);
        }
        $_game = $_game_model->get($_experiment['game_id']);

        $_vdb = new VarDB($_pdo, 'experiment-' . $_experiment['id']);
        $properties = [
            'game' => $_game,
            'experiment' => $_experiment,
            'participants' => $_participant_model->get_all($_experiment['id'])
        ];
        modui($_request, 'game', $_vdb, './game/' . $_game['directory'] . '/admin.php', 5000, $properties);
    }
}else{
    redirect_uri(_URL);
}
