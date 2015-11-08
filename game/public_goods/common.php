<?php

// constant values

// experiment settings
define('EXP_NO', 'password');
define('NUM_PLAYER', 2);
define('MAX_TURN', 3);

// pages
define('PAGE_WAIT', 'wait');
define('PAGE_REJECT', 'reject');
define('PAGE_EXPERIMENT', 'experiment');
define('PAGE_WAIT_ACTION', 'waitAction');
define('PAGE_MIDDLE_RESULT', 'middleResult');
define('PAGE_FINAL_RESULT', 'finalResult');
define('PAGE_TEST', 'uiTest');

// variables
define('VAR_ID', 'id');
define('VAR_ACTIVE', 'active');
define('VAR_STATUS', 'status');
define('VAR_PAGE', 'page');
define('VAR_TURN', 'turn');
define('VAR_CUR_ID', 'cur_id');
define('VAR_CUR_PT', 'cur_pt');
define('VAR_TOTAL_PROFIT', 'total_profit');
define('VAR_INVEST_PT', 'invest_pt');
define('VAR_PUNISH_PT', 'punish_pt');
define('VAR_PUNISH_TARGET', 'punish_target');
define('VAR_READY', 'ready');


// common functions 
function setValueToAllUsers($con, $id, $val)
{
    foreach ( $con->participants as $participant ) {
        $con->set_personal($id, $val, $participant[VAR_ID]);
    }
}

function calcNumReadyUser($con) {
    $num_ready_user = 0;
    foreach ( $con->participants as $participant ) {
        $is_ready = $con->get_personal(VAR_READY, false, $participant[VAR_ID]);
        if ( $is_ready ) {
            ++$num_ready_user;
        }
    }
    
    return $num_ready_user;
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

function isReady($num_ready_user)
{
    return ($num_ready_user == NUM_PLAYER);
}

function calcTotalInvestment($con)
{
    $total = 0;
    foreach ( $con->participants as $participant ) {
        $total += $con->get_personal(VAR_INVEST_PT, 0, $participant[VAR_ID]);
    }

    return $total;
}

function calcProfit($con, $total_investment)
{
    $cur_pt = $con->get_personal(VAR_CUR_PT, 20);
    $invest_pt = $con->get_personal(VAR_INVEST_PT, 0);

    return $cur_pt - $invest_pt + 0.4*$total_investment;
}

function setTotalProfit($con)
{
    $total_investment = calcTotalInvestment($con);
    $profit = calcProfit($con, $total_investment);
    $cur_total_profit = $con->get_personal(VAR_TOTAL_PROFIT, 0);
    $con->set_personal(VAR_TOTAL_PROFIT, $cur_total_profit + $profit);
}

function inclementTurn($con)
{
    $turn = $con->get(VAR_TURN, 1);
    ++$turn;
    $con->set(VAR_TURN, $turn);

    return $turn; 
}

function isFinish($turn)
{
    return ($turn > MAX_TURN);
}

function sortProfitList($total_profit_list)
{
    usort($total_profit_list, 
        function($a, $b) {
            return ($a['pt'] < $b['pt']);
        }    
    );

    return $total_profit_list;
}

