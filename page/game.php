<?php

if($_participant_session->is_login){
    $_participant = $_participant_model->get($_participant_session->user_id);
    $_experiment = $_experiment_model->get($_participant['experiment_id']);
    switch($_experiment['status']){
    case ExperimentModel::S_REMOVED:
        redirect_uri(_URL);
    case ExperimentModel::S_RESERVED:
        //TODO
        break;
    case ExperimentModel::S_FINISHED:
        //TODO
        break;
    case ExperimentModel::S_RUNNING:
        $_game = $_game_model->get($_experiment['game_id']);
        if($_api){
            $vdb = new VarDB($_pdo, '_experiment_' . $experiment_id);
            require DIR_ROOT . 'game/' . $_game['directory'] . '/api.php';
        }else{
            $_tmpl = new Template();
            $_tmpl->add('<script src="' . _URL . 'js/game.js' . '"></script>');
            echo $_tmpl->display();
        }
        break;
    }
}else{
    redirect_uri(_URL);
}
