<?php

require 'common.php';

// page settings
$pages = [];
$pages[PAGE_REJECT]             = new RedirectUI(_URL, $_con->get_personal(VAR_PAGE, PAGE_WAIT) == PAGE_REJECT);
$pages[PAGE_WAIT]               = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages[PAGE_EXPERIMENT]         = new NormalContainer();
$pages[PAGE_PUNISHMENT]         = new NormalContainer();
$pages[PAGE_WAIT_ACTION]        = new NormalContainer();
$pages[PAGE_PUNISHMENT_RESULT]  = new NormalContainer();
$pages[PAGE_MIDDLE_RESULT]      = new NormalContainer();
$pages[PAGE_FINAL_RESULT]       = new NormalContainer();


$pages[PAGE_EXPERIMENT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
You have {cur_pt} points.<br/>
And, your sum of profit is {total_profit} points.<br/>
What point do you invest?<br/>
TMPL
,   function()use($_con) {
        return ['turn' => $_con->get(VAR_TURN, 0), 'id' => $_con->get_personal(VAR_CUR_ID, 0), 'cur_pt' => $_con->get_personal(VAR_CUR_PT), 'total_profit' => $_con->get_personal(VAR_TOTAL_PROFIT)];
    }
));

$pages[PAGE_EXPERIMENT]->add(new SendingUI('invest', 
    function($value)use($_con) {
        setValueToAllUsers($_con, VAR_CUR_PT, 20);
        setValueToAllUsers($_con, VAR_INVEST_PT, 0);
        setValueToAllUsers($_con, VAR_PUNISH_PT, 10);
        setValueToAllUsers($_con, VAR_RECEIVED_PUNISH_PT, 0);

        $invest_pt = intval($value);
        $cur_pt = $_con->get_personal(VAR_CUR_PT);
        if ( $invest_pt < 0 || $invest_pt > $cur_pt ) {
            return;
        }

        $_con->set_personal(VAR_INVEST_PT, $invest_pt);
        $_con->set_personal(VAR_READY, true);

        if ( isReady(calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_MIDDLE_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_PUNISHMENT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
You have {punish_pt} points for punishment.<br/>
What point do you use for punishment.<br/>
TMPL
,   function()use($_con) {
        return [
            'turn'          => $_con->get(VAR_TURN, 0), 
            'id'            => $_con->get_personal(VAR_CUR_ID, 0), 
            'punish_pt'     => $_con->get_personal(VAR_PUNISH_PT), 
        ]; 
    }
));

$pages[PAGE_PUNISHMENT]->add(new MultiSendingUI('OK',
    function()use($_con) {
        $list = [];
        foreach ( $_con->participants as $participant ) {
            $id = $participant['id'];
            $invest_pt = $_con->get_personal(VAR_INVEST_PT, 0, $id);
            $description = 'ID:' . $id . ' Investment point:' . $invest_pt . ' ';
            $list[] = [
                'id'            => $id,
                'description'   => $description,
            ];
        }

        return $list; 
    },
    function($value)use($_con) {
        $total_punish = 0;
        foreach ( $value as $id => $punish_pt ) {
            $total_punish += $punish_pt;
        }
        if ( $punish_pt < 0 || $punish_pt > 10 ) {
            return;
        }

        foreach ( $value as $id => $punish_pt ) {
            $received_punish_pt = $_con->get_personal(VAR_RECEIVED_PUNISH_PT, 0, $id);
            $_con->set_personal(VAR_RECEIVED_PUNISH_PT, $received_punish_pt + $punish_pt*3, $id);  
        }

        $_con->set_personal(VAR_READY, true); 
        if ( isReady(calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_PUNISHMENT_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_WAIT_ACTION]->add(new TemplateUI(<<<TMPL
Waiting for {num_not_ready_user} users...<br/>
TMPL
,   function()use($_con) { 
        $num_not_ready_user = NUM_PLAYER - calcNumReadyUser($_con);

        return ['num_not_ready_user' => $num_not_ready_user];
    }
));


$pages[PAGE_PUNISHMENT_RESULT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
Punishment Result<br/>
{each punish_list}
<span>ID:{id} Recieved Punishment Point:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $turn = $_con->get(VAR_TURN, 1);
        $punish_list = [];
        foreach ( $_con->participants as $participant ) {
            $id = $participant[VAR_ID];
            $pt = $_con->get_personal(VAR_RECEIVED_PUNISH_PT, 0, $id);
            $punish_list[] = ['id' => $id, 'pt' => $pt];
        } 

        return ['turn' => $turn, 'id' => $_con->get_personal(VAR_CUR_ID, 0), 'punish_list' => $punish_list];
    }
));

$pages[PAGE_PUNISHMENT_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        $total_profit = $con->get_personal(VAR_TOTAL_PROFIT, 0);
        $punish_pt = $con->get_personal(VAR_PUNISH_PT, 0);
        $received_punish_pt = $con->get_personal(VAR_RECEIVED_PUNISH_PT, 0);
        $con->set_personal(VAR_TOTAL_PROFIT, $total_profit - $punish_pt - $received_punish_pt);

        $con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($con)) ) {
            $turn = inclementTurn($turn);
            if ( isFinish($turn) ) {
                redirectAllUsers($con, PAGE_FINAL_RESULT); 
            } else {
                redirectAllUsers($con, PAGE_EXPERIMENT);
            } 
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_MIDDLE_RESULT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
Middle Result<br/>
{each invest_list}
<span>ID:{id} Investment Point:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $turn = $_con->get(VAR_TURN, 1);
        $invest_list = [];
        foreach ( $_con->participants as $participant ) {
            $id = $participant[VAR_ID];
            $pt = $_con->get_personal(VAR_INVEST_PT, 0, $id);
            $invest_list[] = [VAR_ID => $id, 'pt' => $pt];
        } 

        return ['turn' => $turn, 'id' => $_con->get_personal(VAR_CUR_ID, 0), 'invest_list' => $invest_list];
    }
));

$pages[PAGE_MIDDLE_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        setTotalProfit($con);

        $con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($con)) ) {
            if ( isPunishPhase($con) ) {
                redirectAllUsers($con, PAGE_PUNISHMENT);
            } else {
                $turn = inclementTurn($con);
                if ( isFinish($turn) ) {
                    redirectAllUsers($con, PAGE_FINAL_RESULT); 
                } else {
                    redirectAllUsers($con, PAGE_EXPERIMENT);
                } 
            }
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_FINAL_RESULT]->add(new TemplateUI(<<<TMPL
Final Result<br/>
Your ID:{id}<br/>
{each total_profit_list}
<span>ID:{id} Total Profit:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $total_profit_list = [];
        foreach ( $_con->participants as $participant ) {
            $id = $participant[VAR_ID];
            $pt = $_con->get_personal(VAR_TOTAL_PROFIT, 0, $id);
            $total_profit_list[] = ['id' => $id, 'pt' => $pt];
        } 

        $total_profit_list = sortProfitList($total_profit_list);
        
        return ['id' => $_con->get_personal(VAR_CUR_ID, 0), 'total_profit_list' => $total_profit_list];
    }
));


// add all pages
$_con->add_component($_page = new PageContainer(
    function()use($_con) { 
        return $_con->get_personal(VAR_PAGE, PAGE_WAIT); 
    }
));
foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

