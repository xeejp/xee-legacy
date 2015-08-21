<?php
if($_host_session->is_login){
    $_host = $_host_model->get($_host_session->user_id);
    if($_request->request_method === Request::GET){
        $_games = $_game_model->get_all();
        $_experiments = $_experiment_model->get_all_by_host($_host['id']);
        $_data = ['token_name' => _TOKEN, 'token' => get_token('host')];
        $_data['games'] = $_games;
        $_data['experiments'] = $_experiments;
        $_template = '';
        $running = ExperimentModel::S_RUNNING;
        $_template .= <<<TMPL
[games]<br/>
<form action="./host/games" method="post">
{each games}
ID: {id}, name: {name};
<input type="submit" name="game_id" value="{id}"></input><br/>
{/each}
<input type="hidden" name="{token_name}" value="{token}"></input>
</form>
[experiments]<br/>
<form action="./host/experiments" method="post">
{each experiments}
ID: {id} , gameID: {game_id};
{switch status}
{case $running}
<a href="./admin/{id}">admin</a>
{/switch}
<input type="submit" name="experiment_id" value="{id}"></input><br/>
{/each}
<input type="hidden" name="{token_name}" value="{token}"></input>
</form><br/>
TMPL;
        $_tmpl = new Template();
        $_tmpl->lwte_add('host', $_template);
        $_tmpl->lwte_use('#container', 'host', $_data);
        echo $_tmpl->display();
    }else{
        if(!check_token('host', $_request->get_string(_TOKEN))){
            switch($_request->get_uri()){
            case 'games':
                if((($_game_id = $_request->get_string('game_id')) !== null) &&
                        ($_game_model->exist_id($_game_id))){
                    $_experiment_model->insert($_host['id'], $_game_id);
                }
                break;
            case 'experiments':
                if((($_experiment_id = $_request->get_string('experiment_id')) !== null) &&
                        ($_experiment_model->exist_id($_experiment_id))){
                    $_experiment_model->set_status($_experiment_id, ExperimentModel::S_RUNNING);
                    redirect_uri(_URL . 'admin/' . $_experiment_id);
                }
                break;
            }
        }
        redirect_uri(_URL . 'host');
    }
}else{
    //game list
    $_games = $_game_model->get_all();
    $_data = [];
    foreach($_games as $_game){
        $_data['games'][] = ['id' => $_game['id'], 'name' => $_game['name']];
    }
    $_tmpl = new Template();
    $_tmpl->lwte_add('host', <<<TMPL
[games]<br/>
{each games}
ID: {id}, name: {name};<br/>
{/each}
TMPL
    );
    $_tmpl->lwte_use('#container', 'host', $_data);
    echo $_tmpl->display();
}
