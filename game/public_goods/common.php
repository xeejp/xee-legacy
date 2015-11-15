<?php

// constant values

// experiment settings
define('EXP_NO', 'password');
define('NUM_PLAYER', 2);
define('MAX_TURN', 6);
define('DEFAULT_TURN', 6);

// pages
define('PAGE_WAIT', 'wait');
define('PAGE_REJECT', 'reject');
define('PAGE_EXPERIMENT', 'experiment');
define('PAGE_PUNISHMENT', 'punishment');
define('PAGE_WAIT_ACTION', 'waitAction');
define('PAGE_PUNISHMENT_RESULT', 'punishmentResult');
define('PAGE_MIDDLE_RESULT', 'middleResult');
define('PAGE_FINAL_RESULT', 'finalResult');
define('PAGE_GRAPH', 'graph');

// variables
define('VAR_ID', 'id');
define('VAR_ACTIVE', 'active');
define('VAR_STATUS', 'status');
define('VAR_PAGE', 'page');
define('VAR_TURN', 'turn');
define('VAR_TOTAL_TURN', 'total_turn');
define('VAR_TURN_NO_PUNISH', 'turn_no_punish');
define('VAR_TURN_PUNISH', 'turn_punish');
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
    $cur_pt     = $con->get_personal(VAR_CUR_PT, 0);
    $invest_pt  = $con->get_personal(VAR_INVEST_PT, 0);

    return (float)$cur_pt - (float)$invest_pt + 0.4*(float)$total_investment;
}

function setTotalProfit($con)
{
    $total_investment   = calcTotalInvestment($con);
    $profit             = calcProfit($con, $total_investment);
    $total_profit       = $con->get_personal(VAR_TOTAL_PROFIT, 0);
    $total_profit       += (float)($profit);
    $con->set_personal(VAR_TOTAL_PROFIT, $total_profit);
}

function reduceTotalProfit($con)
{
    $punish_pt          = $con->get_personal(VAR_PUNISH_PT, 0);
    $received_punish_pt = $con->get_personal(VAR_RECEIVED_PUNISH_PT, 0);
    $total_profit       = $con->get_personal(VAR_TOTAL_PROFIT, 0);
    $total_profit       -= (float)($punish_pt + $received_punish_pt);
    $con->set_personal(VAR_TOTAL_PROFIT, $total_profit);

}

function inclementTurn($con)
{
    $turn = $con->get(VAR_TURN, 0);
    ++$turn;
    $con->set(VAR_TURN, $turn);

    $total_turn = $con->get(VAR_TOTAL_TURN, 0);
    ++$total_turn;
    $con->set(VAR_TOTAL_TURN, $total_turn);

    return $turn; 
}

function isPunishPhase($con)
{
    return $con->get(VAR_PUNISH_PHASE, false);
}

function isFinishCurrentPhase($con, $turn)
{
    $punish_phase   = isPunishPhase($con);
    $turn_no_punish = strval($con->get(VAR_TURN_NO_PUNISH));
    $turn_punish    = strval($con->get(VAR_TURN_PUNISH));

    return (
        (!$punish_phase && $turn > $turn_no_punish)
        || ($punish_phase && $turn > $turn_punish)
    );
}

function isFinishAllPhase($con)
{
    $total_turn = $con->get(VAR_TOTAL_TURN);
    $turn_no_punish = strval($con->get(VAR_TURN_NO_PUNISH));
    $turn_punish    = strval($con->get(VAR_TURN_PUNISH));


    return ($total_turn > ($turn_no_punish + $turn_no_punish));
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


function isCurrentUser($con, $id)
{
    $cur_id = $con->get_personal(VAR_CUR_ID, 0); 

    return ( strval($id) == strval($cur_id) );
}

function calcTotalPunishment($value)
{
    $total_punish = 0;
    foreach ( $value as $id => $punish_pt ) {
        $pt             = intval($punish_pt);
        $total_punish   += $pt;
    }
    
    return $total_punish;
}

function isValidValue($value, $min, $max)
{
    return ($min <= $value && $value <= $max);
}

function initAllUsersData($con)
{
    foreach ( $con->participants as $participant ) {
        $id = strval($participant[VAR_ID]);
        setValueToAllUsers($con, VAR_CUR_PT, 20, $id);
        setValueToAllUsers($con, VAR_CUR_PUNISH_PT, 10, $id);
        setValueToAllUsers($con, VAR_INVEST_PT, 0, $id);
        setValueToAllUsers($con, VAR_PUNISH_PT, 0, $id);
        setValueToAllUsers($con, VAR_RECEIVED_PUNISH_PT, 0, $id);
    }
}

function initCurrentUserData($con)
{
    setValueToAllUsers($con, VAR_CUR_PT, 20);
    setValueToAllUsers($con, VAR_CUR_PUNISH_PT, 10);
    setValueToAllUsers($con, VAR_INVEST_PT, 0);
    setValueToAllUsers($con, VAR_PUNISH_PT, 0);
    setValueToAllUsers($con, VAR_RECEIVED_PUNISH_PT, 0); 
}

function calcReceivedPunishment($con, $id, $punish_pt)
{
    $pt                 = intval($punish_pt); 
    $received_punish_pt = $con->get_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id));
    $received_punish_pt += 3*$pt;
    $con->set_personal(VAR_RECEIVED_PUNISH_PT, $received_punish_pt, strval($id));
}

function changePhase($con)
{
    $current_phase = $con->get(VAR_PUNISH_PHASE);
    if ( $current_phase ) {
        $current_phase = false;
    } else {
        $current_phase = true;
    }
    $con->set(VAR_PUNISH_PHASE, $current_phase);
}
