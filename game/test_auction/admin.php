<?php

$_loader = new ClassLoader();
$_loader->register_directory(__DIR__ . '/com');
$_loader->register();

$container = new NormalContainer();
// options
$container->add(new StaticUI('[設定]<br/>'));
$container->add(new StaticUI('ExpID : '. $_con->experiment['password'] .'<br/>'));
$container->add(new StaticUI('損失を許可する : '));
$container->add(new OptionUI($_con, 'allow_loss', $_con->get('allow_loss')));

// participants
$container->add(new ParticipantsList($_con));
$container->add(new ParticipantsManagement($_con));

$container->add($modulator = new PageContainer($_con->get('status', 'wait')));
$modulator->add('wait', new MatchingButton($_con,
    function($con){
        $num = 0;
        foreach ($con->participants as $participant) {
            if ($con->get_personal('active', false, $participant['id']))
                $num++;
        }
        return ($num>0) && ($num%2 == 0);
    },
    function($con){
        $count = 1;
        $result = [];
        foreach($con->participants as $participant){
            if (!$con->get_personal('active', false, $participant['id'])) continue;
            $con->set_personal('role', ($count % 2 == 1)? 'seller': 'buyer', $participant['id']);
            if ($con->get_personal('role', null, $participant['id']) == 'seller') {
                $con->set_personal('money', 0, $participant['id']);
                $con->set_personal('cost', $count * 100, $participant['id']);
            } else {
                $con->set_personal('money', $count * 100, $participant['id']);
                $con->set_personal('cost', 0, $participant['id']);
            }
            $con->set_personal('price', 0, $participant['id']);
            $count++;
        }
        $con->set('status', 'ready');
        return $result;
    }
));
$modulator->add('ready', $_ready = new NormalContainer());
$_ready->add(new ButtonUI($_con,
    function($_con){ return "再マッチング"; },
    function($_con){ $_con->set('status', 'wait'); }
));
$_ready->add(new ButtonUI($_con,
    function($_con){ return "開始"; },
    function($_con){
        $_con->set('status', 'experiment');
        foreach ($_con->participants as $participant) {
            if ($_con->get_personal('active', false, $participant['id']))
                $_con->set_personal('page', 'experiment', $participant['id']);
            else
                $_con->set_personal('page', 'reject', $participant['id']);
        }
    }
));
$modulator->add('experiment', new ButtonUI($_con,
    function($_con){ return 'リセット'; },
    function($_con){
        $_con->set('status', 'wait');
        foreach ($_con->participants as $participant) {
            $_con->set_personal('page', 'reject', $participant['id']);
        }
    }
));

$_con->add_component($container);
