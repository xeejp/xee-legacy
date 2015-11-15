<?php

require 'common.php';

$container = new NormalContainer();
// options
$container->add(new StaticUI('[設定]<br/>'));
$container->add(new StaticUI('ExpID : '. $_con->experiment[EXP_NO] .'<br/><br/>'));

// settings
$container->add(new MultiSendingUI('Update',
    call_user_func(
        function($con) {
            $list = [
                ['id' => VAR_TURN_NO_PUNISH,    'description' => 'The number of turn of no-punishment '],
                ['id' => VAR_TURN_PUNISH,       'description' => 'The number of turn of punishment ']
            ];

            return $list;
        }
        ,$_con
    ),
    function($value)use($_con) {
        $turn_no_punish = intval($value[VAR_TURN_NO_PUNISH]);
        $turn_punish    = intval($value[VAR_TURN_PUNISH]);
        if ( !(isValidValue($turn_no_punish, 1, MAX_TURN) || isValidValue($turn_punish, 1, MAX_TURN)) ) {
            return;
        }

        $_con->set(VAR_TURN_NO_PUNISH, $turn_no_punish);
        $_con->set(VAR_TURN_PUNISH, $turn_punish); 
    }
)); 

$container->add(new TemplateUI(<<<TMPL
<br/>
No-Punishment: {turn_no_punish}<br/>
Punishment: {turn_punish}<br/>
<br/>
TMPL
,   function()use($_con) {
        $list = [
            'turn_no_punish'    => strval($_con->get(VAR_TURN_NO_PUNISH, 0)),
            'turn_punish'       => strval($_con->get(VAR_TURN_PUNISH, 0))
        ];

        return $list;
    } 
));

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
            $active = $con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID])); 
            if ( $active ) {
                $num++;
            }
        }

        return ($num == NUM_PLAYER);
    },
    function($con) {
        $result = [];
        foreach ( $con->participants as $participant ) {
            $id     = $participant[VAR_ID];
            $active = $con->get_personal(VAR_ACTIVE, false, strval($id));
            if ( !$active ) {
                continue;
            }

            $con->set_personal(VAR_CUR_ID, $id, strval($id));
            $con->set_personal(VAR_CUR_PT, 20, strval($id));
            $con->set_personal(VAR_CUR_PUNISH_PT, 10, strval($id));
            $con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id));
            $con->set_personal(VAR_INVEST_PT, 0, strval($id));
            $con->set_personal(VAR_PUNISH_PT, 0, strval($id));
            $con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id)); 
            $con->set_personal(VAR_READY, false, strval($id));
            $con->set_personal(ARRAY_INVEST_PT, '', strval($id));

            $con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($id));
        }
        $con->set(VAR_TURN, 1);
        $con->set(VAR_TOTAL_TURN, 1);
        $con->set(VAR_PUNISH_PHASE, false);
        $con->set(VAR_PAGE, 'ready');

        if ( $con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ( $con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }

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
            if ($_con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID]))) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPERIMENT, strval($participant[VAR_ID]));
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
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
            $_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($participant[VAR_ID]));
        }
    }
));


$_con->add_component($container);
