<?php

// page settings
$pages = [];
$pages['reject']        = new RedirectUI(_URL, $_con->get_personal('page', 'wait') == 'reject');
$pages['wait']          = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages['experiment']    = new NormalContainer();
$pages['wait_action']   = new NormalContainer();
$pages['middle_result'] = new NormalContainer();
$pages['final_result']  = new NormalContainer();
    

function setValueToAllUsers($con, $id, $val)
{
    foreach ( $con->participants as $participant ) {
        $con->set_personal($id, $val, $participant['id']);
    }
}

function calc_num_not_ready_user($con) {
    $num_not_ready_user = 0;
    foreach ( $con->participants as $participant ) {
        $is_ready = $con->get_personal('ready', false, $participant['id']);
        if ( !$is_ready ) {
            ++$num_not_ready_user;
        }
    }
    
    return $num_not_ready_user;
}

function redirectAllUsers($con, $page_id)
{
    foreach( $con->participants as $participant ) {
        $con->set_personal('status', $page_id, $participant['id']);
        $con->set_personal('page', $page_id, $participant['id']);
    }
}

function redirectCurrentUser($con, $page_id)
{
    $con->set_personal('status', $page_id);
    $con->set_personal('page', $page_id);
}

$pages['experiment']->add(new TemplateUI(<<<TMPL
Turn: {turn}<br/>
You have {cur_pt} points.<br/>
What point do you invest?<br/>
TMPL
,   ['turn' => $_con->get('turn', 0), 'cur_pt' => $_con->get_personal('cur_pt')]
));

$pages['experiment']->add(new SendingUI('invest', 
    function($value)use($_con) {
        $invest_pt = intval($value);
        $cur_pt = $_con->get_personal('cur_pt');
        if ( $invest_pt < 0 || $invest_pt > $cur_pt ) {
            return;
        }

        $_con->set_personal('invest_pt', $invest_pt);
        $_con->set_personal('ready', true);

        $num_not_ready_user = calc_num_not_ready_user($_con);
        if ( $num_not_ready_user == 0 ) {
            if ( turn < 6 ) {
                redirectAllUsers($_con, 'middle_result');
            } else {
                redirectAllUsers($_con, 'final_result'); 
            }
            setValueToAllUsers($_con, 'ready', false);
        } else {
            redirectCurrentUser($_con, 'wait_action');
        }
    }
));


$pages['wait_action']->add(new TemplateUI(<<<TMPL
Waiting for {num_not_ready_user} users...<br/>
TMPL
,   call_user_func(function($con) { 
        $num_not_ready_user = calc_num_not_ready_user($con);

        return ['num_not_ready_user' => $num_not_ready_user];
    }, $_con)
));


$pages['middle_result']->add(new TemplateUI(<<<TMPL
Middle Result<br/>
{each invest_list}
<span>ID:{id} Investment Point:{pt}</span><br/>
{/each}
TMPL
,   call_user_func(function($con) {
        $invest_list = [];
        foreach ( $con->participants as $participant ) {
            $id = $participant['id'];
            $pt = $con->get_personal('invest_pt', 0, $id);
            $invest_list[] = ['id' => $id, 'pt' => $pt];
        } 

        return ['invest_list' => $invest_list];
    }, $_con)
));

function calcTotalInvestment($con)
{
    $total = 0;
    foreach ( $con->participants as $participant ) {
        $total += $con->get_personal('invest_pt', 0, $participant['id']);
    }

    return $total;
}

function calcProfit($con, $total_investment, $id)
{
    $cur_pt = $con->get_person('cur_pt', 20, $id);
    $invest_pt = $con->get_person('invest_pt', 0, $id);

    return $cur_pt - $invest_pt + 0.4*$total_investment;
}

$pages['middle_result']->add(new SendingUI('OK_Result', 
    function($value)use($_con) {
        $total = calcTotalInvestment($_con);
        foreach ( $_con->participants as $participant ) { 
            $id = $participant['id'];
            $profit = calcProfit($_con, $total, $id);
            $_con->set_person('sum_pt', $profit, $id);
        }
        setValueToAllUsers($_con, 'cur_pt', 20);
        setValueToAllUsers($_con, 'invest_pt', 0);

        $turn = $_con->get('turn', 1);
        $_con->set('turn', ++$turn);

        redirectAllUsers($_con, 'wait'); 
    }
));

$pages['middle_result']->add(new StaticUI('UNKO'));


$pages['final_result']->add(new StaticUI('Final Result'));


// add all pages
$_con->add_component($_page = new PageContainer($_con->get_personal('page', 'wait')));
foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

