<?php

require 'common.php';

$container = new NormalContainer();
// options
$container->add(new StaticUI('[設定]<br/>'));
$container->add(new StaticUI('ExpID : '. $_con->experiment[EXP_NO] .'<br/>'));

// participants
$container->add(new ParticipantsList($_con));
$container->add(new ParticipantsManagement($_con));

$container->add($modulator = new PageContainer(
    function()use($_con) {
        return $_con->get(VAR_PAGE, PAGE_WAIT); 
    }
));

$modulator->add_page(PAGE_WAIT, new MatchingButton($_con,
    function($con) {
        $num = 0;
        foreach ( $con->participants as $participant ) {
            $active = $con->get_personal(VAR_ACTIVE, false, $participant[VAR_ID]); 
            if ( $active ) {
                $num++;
            }
        }

        return ($num == NUM_PLAYER);
    },
    function($con) {
        $result = [];
        foreach ( $con->participants as $participant ) {
            $active = $con->get_personal(VAR_ACTIVE, false, $participant[VAR_ID]);
            if ( !$active ) {
                continue;
            }

            $con->set_personal(VAR_CUR_PT, 20, $participant[VAR_ID]);
            $con->set_personal(VAR_SUM_PROFIT, 0, $participant[VAR_ID]);
            $con->set_personal(VAR_INVEST_PT, 0, $participant[VAR_ID]);
            $con->set_personal(VAR_PUNISH_PT, 0, $participant[VAR_ID]);
            $con->set_personal(VAR_PUNISH_TARGET, 0, $participant[VAR_ID]); 
            $con->set_personal(VAR_READY, false, $participant[VAR_ID]);
        }
        $con->set(VAR_TURN, 1);
        $con->set(VAR_PAGE, 'ready');

        return $result;
    }
));

$modulator->add_page('ready', $_ready = new NormalContainer());
$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "再マッチング"; 
    },
    function($_con) { 
        $_con->set(VAR_PAGE, PAGE_WAIT);
    }
));
$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "開始"; 
    },
    function($_con) {
        $_con->set(VAR_PAGE, PAGE_EXPERIMENT);
        foreach ($_con->participants as $participant) {
            if ($_con->get_personal(VAR_ACTIVE, false, $participant[VAR_ID])) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPERIMENT, $participant[VAR_ID]);
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, $participant[VAR_ID]);
            }
        }
    }
));
$modulator->add_page(PAGE_EXPERIMENT, new ButtonUI($_con,
    function($_con) {
        return 'リセット'; 
    },
    function($_con) {
        $_con->set(VAR_STATUS, PAGE_WAIT);
        foreach ($_con->participants as $participant) {
            $_con->set_personal(VAR_PAGE, PAGE_WAIT, $participant[VAR_ID]);
        }
    }
));

$_con->add_component($container);
