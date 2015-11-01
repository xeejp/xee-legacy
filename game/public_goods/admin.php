<?php

$container = new NormalContainer();
// options
$container->add(new StaticUI('[設定]<br/>'));
$container->add(new StaticUI('ExpID : '. $_con->experiment['password'] .'<br/>'));

// participants
$container->add(new ParticipantsList($_con));
$container->add(new ParticipantsManagement($_con));

$container->add($modulator = new PageContainer($_con->get('status', 'wait')));
$modulator->add_page('wait', new MatchingButton($_con,
    function($con) {
        $num = 0;
        foreach ($con->participants as $participant) {
            if ($con->get_personal('active', false, $participant['id']))
                $num++;
        }
        return ($num == 2);
    },
    function($con) {
        $result = [];
        foreach($con->participants as $participant) {
            if (!$con->get_personal('active', false, $participant['id'])) {
                continue;
            }

            $con->set_personal('cur_pt', 20, $participant['id']);
            $con->set_personal('sum_pt', 0, $participant['id']);
            $con->set_personal('invest_pt', 0, $participant['id']);
            $con->set_personal('punish_pt', 0, $participant['id']);
            $con->set_personal('punish_id', 0, $participant['id']); 
            $con->set_personal('ready', false, $participant['id']);
        }
        $con->set('status', 'ready');

        return $result;
    }
));

$modulator->add_page('ready', $_ready = new NormalContainer());
$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "再マッチング"; 
    },
    function($_con) { 
        $_con->set('status', 'wait');
    }
));
$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "開始"; 
    },
    function($_con) {
        $_con->set('status', 'experiment');
        foreach ($_con->participants as $participant) {
            if ($_con->get_personal('active', false, $participant['id'])) {
                $_con->set_personal('page', 'experiment', $participant['id']);
            } else {
                $_con->set_personal('page', 'reject', $participant['id']);
            }
        }
    }
));
$modulator->add_page('experiment', new ButtonUI($_con,
    function($_con) {
        return 'リセット'; 
    },
    function($_con) {
        $_con->set('status', 'wait');
        foreach ($_con->participants as $participant) {
            $_con->set_personal('page', 'reject', $participant['id']);
        }
    }
));

$_con->add_component($container);
