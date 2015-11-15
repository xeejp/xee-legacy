<?php

// constant values

// experiment settings
define('EXP_NO', 'password');
define('NUM_PLAYER', 2);
define('MAX_TURN', 2);

// pages
define('PAGE_WAIT', 'wait');
define('PAGE_REJECT', 'reject');
define('PAGE_EXPERIMENT', 'experiment');
define('PAGE_PUNISHMENT', 'punishment');
define('PAGE_WAIT_ACTION', 'waitAction');
define('PAGE_PUNISHMENT_RESULT', 'punishmentResult');
define('PAGE_MIDDLE_RESULT', 'middleResult');
define('PAGE_FINAL_RESULT', 'finalResult');

// variables
define('VAR_ID', 'id');
define('VAR_ACTIVE', 'active');
define('VAR_STATUS', 'status');
define('VAR_PAGE', 'page');
define('VAR_TURN', 'turn');
define('VAR_PUNISH_PHASE', 'punish_phase');
define('VAR_CUR_ID', 'cur_id');
define('VAR_CUR_PT', 'cur_pt');
define('VAR_CUR_PUNISH_PT', 'cur_punish_pt');
define('VAR_TOTAL_PROFIT', 'total_profit');
define('VAR_INVEST_PT', 'invest_pt');
define('VAR_PUNISH_PT', 'punish_pt');
define('VAR_RECEIVED_PUNISH_PT', 'rec_punish_pt');
define('VAR_READY', 'ready');


// common functions 
function setValueToAllUsers($con, $id, $val)
{
    foreach ( $con->participants as $participant ) {
        $con->set_personal($id, $val, strval($participant[VAR_ID]));
    }
}

function calcNumReadyUser($con) {
    $num_ready_user = 0;
    foreach ( $con->participants as $participant ) {
        $is_ready = $con->get_personal(VAR_READY, false, strval($participant[VAR_ID]));
        if ( $is_ready ) {
            ++$num_ready_user;
        }
    }
    
    return $num_ready_user;
}

function redirectAllUsers($con, $page_id)
{
    foreach( $con->participants as $participant ) {
        $con->set_personal(VAR_PAGE, $page_id, strval($participant[VAR_ID])); 
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
        $total += $con->get_personal(VAR_INVEST_PT, 0, strval($participant[VAR_ID]));
    }

    return $total;
}

function calcProfit($con, $total_investment)
{
    $cur_pt = $con->get_personal(VAR_CUR_PT, 0);
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
    $turn = $con->get(VAR_TURN, 0);
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

function isPunishPhase($con)
{
    return $con->get(VAR_PUNISH_PHASE, false);
}

function isCurrentUser($con, $id)
{
    $cur_id = $con->get_personal(VAR_CUR_ID, 0); 

    return ( strval($id) == strval($cur_id) );
}

function calcTotalPunishment($value)
{
    $total_punish = 0;
    foreach ( $value as $id => $punish_pt ) {
        $pt = intval($punish_pt);
        $total_punish += $pt;
    }
    
    return $total_punish;
}

function isValidValue($value, $min, $max)
{
    return ($min <= $value && $value <= $max);
}
