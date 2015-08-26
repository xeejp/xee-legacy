<?php

$_con->add_component($page = new PageContainer($_con->get('status', 'matching')));
$page->add('matching', $matching = new NormalContainer());
$page->add('game', $game = new NormalContainer());
$page->add('ranking', $ranking = new NormalContainer());

$_con->load_personal_all('buyer', 'money', 'money2');
$matching->add(new StaticUI('<p>マッチング中であああああ</p>'));
$game->add(new PrototypeUI([
    '_con' => $_con,
    'templates' => function($obj, $name){
        return [$name => <<<TMPL
<p>あなたは{if buyer}買い手{else}売り手{/if}です</p>
<p>{money}円{if buyer}の予算があります{else}で生産できます{/if}</p>
{if money2}<p>今{money2}円で{if buyer}買い取り{else}売り出し{/if}中です</p>{/if}
TMPL
        ];
    },
    'values' => function($obj, $name){
        return [
            'buyer' => $obj->_con->buyer,
            'money' => $obj->_con->money,
            'buyer' => $obj->_con->money2 == null ? false : $obj->_con->money2,
        ];
    }
]));
$game->add(new PrototypeUI([
    '_con' => $_con,
    'check_money' => function ($participant1, $participant2){
        return $participant1->money == $participant2['money'] || $participant1->money > $participant2['money2'] == $participant1->buyer;
    },
    'templates' => function($obj, $name){
        return [
            $name => "{use $name-list list1}{use $name-list list2}",
            $name . '-list' => <<<TMPL
{each list}
<p>{money2}円で{if buyer}買い取り{else}売り出し{/if}中{if button}<button>{if buyer}買う{else}売る{/if}</button>{/if}</p>
{/each}
TMPL
        ];
    },
    'values' => function($obj, $name){
        return [
            'list1' => ['list' => $obj->_con->filter_participants(function($_con, $participant){
                if($participant['buyer'] == $_con->buyer){
                    return [];
                }
                return ['buyer', ['money', $participant['money2']], ['button', call_user_func($_con->check_money, $_con, $participant)]];
            })],
            'list2' => ['list' => $obj->_con->filter_participants(function($_con, $participant){
                if($participant['buyer'] != $_con->buyer){
                    return [];
                }
                return ['buyer', 'money'];
            })],
        ];
    },
]));
$game->add(new PrototypeUI([
    '_con' => $_con,
    'templates' => function($obj, $name){
        return [
            $name => <<<TMPL
<input id="$name-momey" type="test">
<button id="$name-b">この値段で{if buyer}買い取る{else}売り出す{/if}</button>
TMPL
        ];
    },
]));
