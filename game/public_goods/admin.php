<?php

require 'common.php';

$container = new NormalContainer();
// options
$container->add(new StaticUI('<div class="container"><div class="page-header"><div align="center">'));
$container->add(new StaticUI('<h1>管理画面</h1><hr/>'));
$container->add(new StaticUI('<h2>実験番号: <font style="color:red;">'. $_con->experiment[EXP_NO] .'</font></h2></div><hr/>'));


// settings
$container->add(new MultiSendingUI('設定',
    call_user_func(
        function($con) {
            $list = [
                ['id' => VAR_NUM_PLAYER,        'description' => '1グループあたりの人数',       'dvalue' => DEFAULT_NUM_PLAYER],
                ['id' => VAR_TURN_NO_PUNISH,    'description' => '罰なし実験の繰り返し回数',    'dvalue' => $con->get(VAR_TURN_NO_PUNISH, 0)],
                ['id' => VAR_TURN_PUNISH,       'description' => '罰あり実験の繰り返し回数',    'dvalue' => $con->get(VAR_TURN_PUNISH, 0)]
            ];

            return $list;
        }
        ,$_con
    ),
    function($value)use($_con) {
        $num_player     = intval($value[VAR_NUM_PLAYER]);
        $turn_no_punish = intval($value[VAR_TURN_NO_PUNISH]);
        $turn_punish    = intval($value[VAR_TURN_PUNISH]);
        if ( !(isValidValue($turn_no_punish, 1, MAX_TURN) || isValidValue($turn_punish, 1, MAX_TURN)) ) {
            return;
        }

        $_con->set(VAR_NUM_PLAYER, $num_player);
        $_con->set(VAR_TURN_NO_PUNISH, $turn_no_punish);
        $_con->set(VAR_TURN_PUNISH, $turn_punish); 
    }
)); 

$container->add(new TemplateUI(<<<TMPL
<br/>
現在の設定値<br/>
1グループあたりの人数：{if num_player==0}未設定{else}{num_player}{/if}<br/>
罰なし: {if turn_no_punish==0}未設定{else}{turn_no_punish}回{/if}<br/>
罰あり: {if turn_punish==0}未設定{else}{turn_punish}回{/if}<br/>
<br/>
TMPL
,   function()use($_con) {
        if ( $_con->get(VAR_NUM_PLAYER, 0) == 0 ) {
            $_con->set(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);
        }
        if ( $_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ( $_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
        $list = [
            'num_player'        => strval($_con->get(VAR_NUM_PLAYER, 0)),
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

        return ($num % $con->get(VAR_NUM_PLAYER, 1) == 0);
    },
    function($con) {
        $result = [];
        $num    = 0;
        foreach ( $con->participants as $participant ) {
            $id     = $participant[VAR_ID];
            $active = $con->get_personal(VAR_ACTIVE, false, strval($id));
            if ( !$active ) {
                continue;
            }

            $group = $num/$con->get(VAR_NUM_PLAYER, 1);
            $con->set_personal(VAR_GROUP, $group, strval($id));

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

            ++$num;
        }
        ++$num;
        $con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, 1);
        $total_turn_array   = array_fill(0, $num, 1); 
        $punish_phase_array = array_fill(0, $num, 0);
        
        $con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));
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
