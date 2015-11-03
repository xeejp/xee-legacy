<?php

require 'common.php';

// page settings
$pages = [];
$pages[PAGE_REJECT]        = new RedirectUI(_URL, $_con->get_personal(VAR_PAGE, PAGE_WAIT) == PAGE_REJECT);
$pages[PAGE_WAIT]          = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages[PAGE_EXPERIMENT]    = new NormalContainer();
$pages[PAGE_WAIT_ACTION]    = new NormalContainer();
$pages[PAGE_MIDDLE_RESULT]  = new NormalContainer();
$pages[PAGE_FINAL_RESULT]   = new NormalContainer();
    

function setValueToAllUsers($con, $id, $val)
{
    foreach ( $con->participants as $participant ) {
        $con->set_personal($id, $val, $participant[VAR_ID]);
    }
}

function calc_num_not_ready_user($con) {
    $num_not_ready_user = 0;
    foreach ( $con->participants as $participant ) {
        $is_ready = $con->get_personal(VAR_READY, false, $participant[VAR_ID]);
        if ( !$is_ready ) {
            ++$num_not_ready_user;
        }
    }
    
    return $num_not_ready_user;
}

function redirectAllUsers($con, $page_id)
{
    foreach( $con->participants as $participant ) {
        $con->set_personal(VAR_PAGE, $page_id, $participant[VAR_ID]); 
    }
}

function redirectCurrentUser($con, $page_id)
{
    $con->set_personal(VAR_PAGE, $page_id);
}

$pages[PAGE_EXPERIMENT]->add(new TemplateUI(<<<TMPL
Turn: {turn}<br/>
You have {cur_pt} points.<br/>
And, your sum of profit is {sum_profit} points.<br/>
What point do you invest?<br/>
TMPL
,   function()use($_con) {
        return [VAR_TURN => $_con->get(VAR_TURN, 0), VAR_CUR_PT => $_con->get_personal(VAR_CUR_PT), VAR_SUM_PROFIT => $_con->get_personal(VAR_SUM_PROFIT)];
    }
));

$pages[PAGE_EXPERIMENT]->add(new SendingUI('invest', 
    function($value)use($_con) {
        $invest_pt = intval($value);
        $cur_pt = $_con->get_personal(VAR_CUR_PT);
        if ( $invest_pt < 0 || $invest_pt > $cur_pt ) {
            return;
        }

        $_con->set_personal(VAR_INVEST_PT, $invest_pt);
        $_con->set_personal(VAR_READY, true);

        $turn = $_con->get(VAR_TURN, 1);

        $num_not_ready_user = calc_num_not_ready_user($_con);
        if ( $num_not_ready_user == 0 ) {
            redirectAllUsers($_con, PAGE_MIDDLE_RESULT);
            setValueToAllUsers($_con, VAR_READY, false);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }

        //dump('pushed invest button', true);
    }
));


$pages[PAGE_WAIT_ACTION]->add(new TemplateUI(<<<TMPL
Waiting for {num_not_ready_user} users...<br/>
TMPL
,   function()use($_con) { 
        $num_not_ready_user = calc_num_not_ready_user($_con);

        return ['num_not_ready_user' => $num_not_ready_user];
    }
));


$pages[PAGE_MIDDLE_RESULT]->add(new TemplateUI(<<<TMPL
Middle Result<br/>
{each invest_list}
<span>ID:{id} Investment Point:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $invest_list = [];
        foreach ( $_con->participants as $participant ) {
            $id = $participant[VAR_ID];
            $pt = $_con->get_personal(VAR_INVEST_PT, 0, $id);
            $invest_list[] = [VAR_ID => $id, 'pt' => $pt];
        } 

        return ['invest_list' => $invest_list];
    }
));

function calcTotalInvestment($con)
{
    $total = 0;
    foreach ( $con->participants as $participant ) {
        $total += $con->get_personal(VAR_INVEST_PT, 0, $participant[VAR_ID]);
    }

    return $total;
}

function calcProfit($con, $total_investment, $id)
{
    $cur_pt = $con->get_personal(VAR_CUR_PT, 20, $id);
    $invest_pt = $con->get_personal(VAR_INVEST_PT, 0, $id);

    return $cur_pt - $invest_pt + 0.4*$total_investment;
}


$pages[PAGE_MIDDLE_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        $total = calcTotalInvestment($con);
        foreach ( $con->participants as $participant ) { 
            $id = $participant[VAR_ID];
            $profit = calcProfit($con, $total, $id); 
            $cur_sum_profit = $con->get_personal(VAR_SUM_PROFIT, 0, $id);
            $con->set_personal(VAR_SUM_PROFIT, $cur_sum_profit + $profit, $id);
        }

        $con->set_personal(VAR_READY, true);
        $num_not_ready_user = calc_num_not_ready_user($con);
        if ( $num_not_ready_user == 0 ) {
            $turn = $con->get(VAR_TURN, 1);
            $con->set(VAR_TURN, ++$turn);
            setValueToAllUsers($con, VAR_CUR_PT, 20);
            setValueToAllUsers($con, VAR_INVEST_PT, 0);
            setValueToAllUsers($con, VAR_READY, false);
            if ( $turn <= 3 ) {
                redirectAllUsers($con, PAGE_EXPERIMENT);
                setValueToAllUsers($con, VAR_READY, false);
            } else {
                redirectAllUsers($con, PAGE_FINAL_RESULT); 
            }
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_FINAL_RESULT]->add(new StaticUI('Final Result'));


// add all pages
$_con->add_component($_page = new PageContainer(
    function()use($_con) { 
        return $_con->get_personal(VAR_PAGE, PAGE_WAIT); 
    }
));

foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

