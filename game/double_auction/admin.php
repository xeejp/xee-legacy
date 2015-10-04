<?php
$_con->load_personal_all('buyer', 'money', 'money2');
$_con->add_component(new StaticUI('<p>実験番号 : ' . $_con->experiment['password'] .'</p>'));
$_con->add_component(new ParticipantsList($_con));
$_con->add_component($page = new PageContainer($_con->get('status', 'matching')));
$page->add_page('matching', $matching = new NormalContainer());
$page->add_page('game', $game = new NormalContainer());
$page->add_page('ranking', $ranking = new NormalContainer());

$matching->add(new MatchingButton($_con,
    function($con){
        if(count($con->participants) != 0 && count($con->participants) % 2 == 0){
            return true;
        }
        return false;
    },
    function($con)use($page){
        $buyer = 0;
        $result = [];
        foreach($con->participants as $key => $participant){
            $result[$participant['id']]['buyer'] = ($buyer ++) % 2 == 0;
            $result[$participant['id']]['money'] = 100 * $buyer;
        }
        $con->set('status', 'game');
        $page->setPage('game');
        return $result;
    })
);
$matching->add(new ParticipantsManagement($_con));
$game->add(new ButtonUI($_con,
    function($_con){
        return "再マッチング";
    },
    function($_con)use($page){
        $_con->set('status', 'matching');
        $page->setPage('matching');
    }
));
