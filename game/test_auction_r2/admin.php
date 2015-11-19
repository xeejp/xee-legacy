<?php

DataController::replace_con($_con, 'global');

$templates = [];
$templates['participants_list'] = <<<'TMPL'
<p>登録済み被験者数：{count}人</p>
<table class="tablesorter" style="border: solid 1px; font-size: 1em;">
<thead>
<tr>
<th>被験者番号</th>
<th>ユーザ名</th>
<th>状態</th>
<th>現在のページ</th>
<th>説明文読了</th>
<th>役割</th>
<th>支払意欲/費用</th>
<th>利益</th>
<th>終了フラグ</th>
</tr>
</thead>
<tbody align="right">
{each participants}
<tr>
<td align="center">{id}</td>
<td align="left">{name}</td>
<td>{if active}参加{else}不参加{/if}</td>
<td align="center">{page}</td>
<td align="center">{read_exp}</td>
{switch role}
{case buyer}
<td>買い手</td>
<td>{money}円</td>
<td>{profit}円</td>
<td>{if finished}終了{else}実験中{/if}</td>
{case seller}
<td>売り手</td>
<td>{cost}円</td>
<td>{profit}円</td>
<td>{if finished}終了{else}実験中{/if}</td>
{default}
<td>未設定</td>
<td></td>
<td></td>
<td></td>
{/switch}
</tr>
{/each}
</tbody>
</table>
<br/>
TMPL;

$container = new NormalContainer();

// options
$container->add(new StaticUI('<div class="container"><div class="page-header"><div align="center">'));
$container->add(new StaticUI('<h1>管理画面</h1><hr/>'));
$container->add(new StaticUI('<h2>実験番号: <font style="color:red;">'. $_con->experiment['password'] .'</font></h2></div><hr/>'));
$container->add(new StaticUI('<div class="pure-form pure-form-stacked"><fieldset><legend>実験設定</legend>'));
$container->add(new StaticUI('<label for="enable_exp">説明を表示しますか。<br/>(初期値は1、非表示にする場合は0を入力してください)</label>'));
$container->add(new OptionUI($_con, 'enable_exp', 1));
$container->add(new StaticUI('<label for="allow_loss">利潤が負となる入力を許可しますか。<br/>(初期値は0、許可する場合は1を入力してください)</label>'));
$container->add(new OptionUI($_con, 'allow_loss', 0));
$container->add(new StaticUI('<label for="tax_buy">買い手に対する課税<br/>(初期値は0)</label>'));
$container->add(new OptionUI($_con, 'tax_buy', 0));
$container->add(new StaticUI('<label for="tax_sell">売り手に対する課税<br/>(初期値は0)</label>'));
$container->add(new OptionUI($_con, 'tax_sell', 0));
$container->add(new StaticUI('</fieldset></div></div>'));

// participants
//$container->add(new ParticipantsList($_con));
$container->add(new TemplateUI($templates['participants_list'], function () use ($_con) {
    $participants = [];
    foreach ($_con->participants as $participant)
        $participants[] = [
            'id' => $participant['id'],
            'name' => $participant['name'],
            'active' => $_con->get_personal('active', true, $participant['id']),
            'page' => $_con->get_personal('page', 'wait', $participant['id']),
            'read_exp' => $_con->get_personal('ExpUI::no', 0, $participant['id']) == 6,
            'role' => $_con->get_personal('role', '', $participant['id']),
            'money' => $_con->get_personal('money', 0, $participant['id']),
            'cost' => $_con->get_personal('cost', 0, $participant['id']),
            'profit' => $_con->get_personal('profit', 0, $participant['id']),
            'finished' => $_con->get_personal('finished', false, $participant['id']),
        ];
    return [
        'count' => count($_con->participants),
        'participants' => $participants,
    ];
}, function () {
    return ['event' => <<<'JS'
function(selector, update){
    $('.tablesorter').tablesorter();
}
JS
];
}));
$container->add(new ParticipantsManagement($_con, true));

// modulator
$container->add($modulator = new PageContainer(function () use($_con) { return $_con->get('status', 'wait'); }));
$modulator->add_page('wait', new MatchingButton($_con,
    function ($_con) {
        $num = 0;
        foreach ($_con->participants as $participant) {
            if ($_con->get('active', true)) $num++;
        }
        return $num > 0;
    },
    function ($_con) {
        $_con->lock();
            $result = [];
            $count = 1;
            $count_max = count($_con->participants) - count($_con->participants)%2;
            $participants = $_con->participants;
            shuffle($participants);
            foreach($participants as $participant){
                if ($count > $count_max || !$_con->get_personal('active', true, $participant['id'])) {
                    $_con->set_personal('page', 'wait', $participant['id']);
                    $_con->set_personal('init', false, $participant['id']);
                    continue;
                }
                $_con->set_personal('ExpUI::no', 0, $participant['id']);
                if ($count % 2 == 1) {
                    $_con->set_personal('role', 'seller', $participant['id']);
                    $_con->set_personal('money', 0, $participant['id']);
                    $_con->set_personal('cost', $count * 100, $participant['id']);
                } else {
                    $_con->set_personal('role', 'buyer', $participant['id']);
                    $_con->set_personal('money', $count * 100, $participant['id']);
                    $_con->set_personal('cost', 0, $participant['id']);
                }
                $_con->set_personal('price', 0, $participant['id']);
                $_con->set_personal('profit', 0, $participant['id']);
                $_con->set_personal('finished', false, $participant['id']);
                $_con->set_personal('init', true, $participant['id']);
                if ($_con->get('enable_exp', true))
                    $_con->set_personal('page', 'ready', $participant['id']);
                else
                    $_con->set_personal('page', 'wait', $participant['id']);
                $count++;
            }
            $_con->set('join_num', $count);
            $_con->set('status', 'ready');
        $_con->unlock();
        return $result;
    }
));
$modulator->add_page('ready', $ready = new NormalContainer());
$ready->add(new ButtonUI($_con,
    function ($_con) { return "再マッチング"; },
    function ($_con) { $_con->set('status', 'wait'); }
));
$ready->add(new ButtonUI($_con,
    function ($_con) { return "実験開始"; },
    function ($_con) {
        foreach ($_con->participants as $participant) {
            if ($_con->get_personal('init', false, $participant['id']))
                $_con->set_personal('page', 'experiment', $participant['id']);
        }
        $_con->set('status', 'experiment');
    }
));
$modulator->add_page('experiment', $experiment = new NormalContainer());
$experiment->add(new ButtonUI($_con,
    function ($_con) { return '再設定'; },
    function ($_con) {
        foreach ($_con->participants as $participant)
            $_con->set_personal('page', 'reject', $participant['id']);
        $_con->set('status', 'wait');
    }
));
$experiment->add(new ButtonUI($_con,
    function ($_con) { return '実験終了'; },
    function ($_con) {
        foreach ($_con->participants as $participant)
            if ($_con->get_personal('init', false, $participant['id']))
                $_con->set_personal('page', 'result', $participant['id']);
            if (!$_con->get_personal('finished', false, $participant['id']))
                $_con->set_personal('price', 0, $participant['id']);
        $_con->set('status', 'wait');
    }
));

$_con->add_component($container);
