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
<h2>game</h2>
<form class="pure-form" action="./host/games" method="post">
<table class="pure-table">
<thead>
<tr><th>ID</th><th>Name</th><th>Reserve</th></tr>
</thead>
<tbody>
{each games}
<tr><td>{id}</td><td>{name}</td>
<td><input class="pure-button" type="submit" name="game_id" value="{id}"></input></td></tr>
{/each}
</tbody>
</table>
<input type="hidden" name="{token_name}" value="{token}"></input>
</form>
<h2>experiments</h2>
<form class="pure-form" action="./host/experiments" method="post">
<table class="pure-table">
<thead>
<tr><th>ID</th><th>ID2</th><th>Admin Page</th><th>Start</th></tr>
</thead>
<tbody>
{each experiments}
<tr><td>{id}</td><td>{game_id}</td><td>
{switch status} {case $running} <a class="pure-button" href="./admin/{id}">admin</a> {/switch}
</td><td><input class="pure-button" type="submit" name="experiment_id" value="{id}"></input></td></tr>
{/each}
</tbody>
</table>
<input type="hidden" name="{token_name}" value="{token}"></input>
</form>
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
<h2>game</h2>
<table class="pure-table">
<thead>
<tr><th>ID</th><th>Name</th></tr>
</thead>
<tbody>
{each games}
<tr><td>{id}</td><td>{name}</td></tr>
{/each}
</tbody>
</table>
TMPL
    );
    $_tmpl->lwte_use('#container', 'host', $_data);
    echo $_tmpl->display();
}
