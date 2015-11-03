<?php

// page settings
$pages = [];
$pages['reject']        = new RedirectUI(_URL, $_con->get_personal('page', 'wait') == 'reject');
$pages['wait']          = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages['experiment']    = new NormalContainer();
$pages['waitAction']    = new NormalContainer();
$pages['middleResult']  = new NormalContainer();
$pages['finalResult']   = new NormalContainer();
    

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
        // $con->set_personal('status', $page_id, $participant['id']);
        $con->set_personal('page', $page_id, $participant['id']);
    }
}

function redirectCurrentUser($con, $page_id)
{
    // $con->set_personal('status', $page_id);
    $con->set_personal('page', $page_id);
}

$pages['experiment']->add(new TemplateUI(<<<TMPL
Turn: {turn}<br/>
You have {cur_pt} points.<br/>
And, your sum of profit is {sum_pt} points.<br/>
What point do you invest?<br/>
TMPL
,   ['turn' => $_con->get('turn', 0), 'cur_pt' => $_con->get_personal('cur_pt'), 'sum_pt' => $_con->get_personal('sum_pt')]
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

        $turn = $_con->get('turn', 1);

        $num_not_ready_user = calc_num_not_ready_user($_con);
        if ( $num_not_ready_user == 0 ) {
            redirectAllUsers($_con, 'middleResult');
            setValueToAllUsers($_con, 'ready', false);
        } else {
            redirectCurrentUser($_con, 'waitAction');
        }

        //dump('pushed invest button', true);
    }
));


$pages['waitAction']->add(new TemplateUI(<<<TMPL
Waiting for {num_not_ready_user} users...<br/>
TMPL
,   call_user_func(function($con) { 
        $num_not_ready_user = calc_num_not_ready_user($con);

        return ['num_not_ready_user' => $num_not_ready_user];
    }, $_con)
));


$pages['middleResult']->add(new TemplateUI(<<<TMPL
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
    $cur_pt = $con->get_personal('cur_pt', 20, $id);
    $invest_pt = $con->get_personal('invest_pt', 0, $id);

    return $cur_pt - $invest_pt + 0.4*$total_investment;
}


$pages['middleResult']->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        $total = calcTotalInvestment($con);
        foreach ( $con->participants as $participant ) { 
            $id = $participant['id'];
            $profit = calcProfit($con, $total, $id); 
            $con->set_personal('sum_pt', $profit, $id);
        }

        $con->set_personal('ready', true);
        $num_not_ready_user = calc_num_not_ready_user($con);
        if ( $num_not_ready_user == 0 ) {
            $turn = $con->get('turn', 1);
            $con->set('turn', ++$turn);
            setValueToAllUsers($con, 'cur_pt', 20);
            setValueToAllUsers($con, 'invest_pt', 0);
            setValueToAllUsers($con, 'ready', false);
            if ( $turn <= 3 ) {
                redirectAllUsers($con, 'experiment');
                setValueToAllUsers($con, 'ready', false);
            } else {
                redirectAllUsers($con, 'finalResult'); 
            }
        } else {
            redirectCurrentUser($con, 'waitAction');
        }
    }
));


$pages['finalResult']->add(new StaticUI('Final Result'));


// add all pages
$_con->add_component($_page = new PageContainer($_con->get_personal('page', 'wait')));
foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

