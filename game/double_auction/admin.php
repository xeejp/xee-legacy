<?php
$_con->load_personal_all('buyer', 'money', 'money2');
$_con->add_component(new ParticipantsList($_con));
$_con->add_component($page = new PageContainer($_con->get('status', 'matching')));
$page->add('matching', $matching = new NormalContainer());
$page->add('game', $game = new NormalContainer());
$page->add('ranking', $ranking = new NormalContainer());

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
$game->add(new ButtonUI($_con,
    function($_con){
        return "再マッチング";
    },
    function($_con)use($page){
        $_con->set('status', 'matching');
        $page->setPage('matching');
    }
));
