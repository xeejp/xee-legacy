<?php

if($_host_session->is_login){
    $_experiment_model = new ExperimentModel($_pdo);
    $_host = $_host_model->get($_host_session->user_id);
    $_experiments = $_experiment_model->get_all_by_host($_host['id']);
}else{
    redirect_uri(_URL);
}
