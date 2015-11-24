<?php

DataController::replace_con($_con, 'global');

// templates
$templates = [];
// wait
$templates['wait'] = <<<'TMPL'
<div style="text-align: center;">
<h1>ダブルオークション</h1>
<hr/>
実験開始までしばらくお待ちください。</div>
TMPL;
// experiment
$templates['experiment']['status'] = <<<'TMPL'
<h1 style="text-align: center;">実験開始</h1>
<hr/><br/>
{switch role}
{case buyer}
あなたの役割は<b>「買い手」</b>です。<br/>
あなたの支払意欲は<b>{money}円</b>です。<br/>
{if tax_sell}買い手に対する課税額は1単位当たり{tax_sell}円です。<br/>{/if}
<br/>
もしあなたが{money}円よりも低い金額で購入すると、あなたの利潤は
<center><i>{money}円－購入価格</i></center>
で計算されます。<br/>
{if allow_loss}{else}({money}円よりも高い価格は入力できません。)<br/>{/if}
{case seller}
あなたの役割は<b>「売り手」</b>です。<br/>
あなたの仕入価格は<b>{cost}円</b>です。<br/>
{if tax_buy}売り手に対する課税額は1単位当たり{tax_buy}円です。<br/>{/if}
<br/>
もしあなたが{cost}円よりも高い価格で販売すると、あなたの利潤は
<center><i>販売価格－{cost}円</i></center>
で計算されます。<br/>
{if allow_loss}{else}({cost}円よりも低い価格は入力できません。)<br/>{/if}
{/switch}
</br>
TMPL;
$templates['experiment']['input'] = <<<'TMPL'
<hr/><br/>
希望価格を入力してください。<br/>
TMPL;
$templates['experiment']['caution'] = <<<'TMPL'
<br/>
{if caution}<b style="color:red;">{caution}</b><br/>{/if}
<br/>
TMPL;
$templates['experiment']['list'] = <<<'TMPL'
{if list}
<hr/><br/>
他の参加者は次の金額を提案しています。<br/>
<table class="">
<tr>
{each list}
<td style="vertical-align:top;">
<table class="pure-table">
<thead><tr><th>{head}</th></tr></thead>
<tbody style="text-align: right;">
{each body}<tr><td{if self} style="background-color: #ccc;"{/if}>{if price}{price}円{/if}</td></tr>{/each}
</tbody>
</table>
</td>
{/each}
</tr>
</table>
<br/>
{/if}
TMPL;
// finished
$templates['finished'] = <<<'TMPL'
<h1 style="text-align: center;">取引成立</h1>
<hr/>
{switch role}
{case buyer}
あなたの提示価格を受諾する売り手が現れました。<br/>
<br/>
あなたは財を{price}円で購入できました。<br/>
あなたはその財に{money}円まで支払っても良いと考えていたので、{profit}円得したことになります。<br/>
{case seller}
あなたの提示価格を受諾する買い手が現れました。<br/>
<br/>
あなたは財を{price}円で売却できました。<br/>
あなたはその財を仕入れるのに{cost}円支払ったので、{profit}円得したことになります。<br/>
{/switch}
<br/>
他の人の取引成立をお待ち下さい。<br/>
TMPL;
// result
$templates['result'] = [];
$templates['result']['personal'] = <<<'TMPL'
<h1 style="text-align: center;">実験結果</h1>
<hr/><br/>
{switch role}
{case buyer}
あなたの役割は<b>「買い手」</b>でした。<br/>
支払意欲:{money}円<br/>
買値:{price}円<br/>
消費者余剰:{profit}円<br/>
{case seller}
あなたの役割は<b>「売り手」</b>でした。<br/>
費用:{cost}円<br/>
売値:{price}円<br/>
生産者余剰:{profit}円<br/>
{/switch}
<br/>
TMPL;
$templates['result']['ranking'] = <<<'TMPL'
<hr/><br/>
<b>ランキング</b><br/>
<table class="tablesorter" style="border: solid 1px; font-size: 1em;">
<thead>
<tr><th>順位</th><th>余剰</th><th>役割</th><th>支払意欲／費用</th><th>取引価格</th></tr>
</thead>
<tbody align="right">
{each ranking}
<tr{if self} class="pure-table-odd"{/if}>
{switch role}
{case buyer}<td>{no}位</td><td>{profit}円</td><td>買い手</td><td>{money}円</td><td>{price}円</td>
{case seller}<td>{no}位</td><td>{profit}円</td><td>売り手</td><td>{cost}円</td><td>{price}円</td>
{/switch}
</tr>
{/each}
</tbody>
<tfoot align="right">
<tr>
<td>結果<br/>理論値</td>
<td>{each profit}<span style="float:left">(総余剰)</span><span style="float:right">{result}円</span><br/><span style="float:left">(総余剰)</span> {theorical}円{/each}<a style="clear:both"></a></td>
<td></td>
<td></td>
<td>{each price}<span style="float:left">(平均)</span><span style="float:right">{result}円</span><br/><span style="float:left">(平均)</span><span style="float:right">{theorical}円</span>{/each}<a style="clear:both"></a></td>
</tr>
</tfoot>
</table>
<br/>
TMPL;
$templates['result']['graph'] = <<<'TMPL'
<hr/><br/>
<b>グラフ</b><br/>
TMPL;

// components
$_con->add_component(new RedirectUI(_URL, ($_con->get_personal('page', 'wait') == 'reject')));
$_con->add_component(new StaticUI('<div><div style="margin: 0 auto; max-width: 40em;">'));
$_con->add_component($page_container = new PageContainer(function () use ($_con) { return $_con->get_personal('page', 'wait'); }));
$_con->add_component(new StaticUI('</div></div>'));

// pages
$page_container->add_page('reject', new StaticUI(''));
$page_container->add_page('wait', new StaticUI($templates['wait']));
$page_container->add_page('ready', $explanation = new ExplanationUI($_con));
switch ($_con->get_personal('role', '')) {
default:
    $explanation->add_page('', []);
    break;
case 'buyer':
    $explanation
      ->add_page('役割決め', [
        ['explanation' => 'これからコンピュータがみなさんを「売り手」と「買い手」の2組に分けます。'],
        ['explanation' => '「次へ」ボタンを押して役割を確認してください。'],
    ])->add_page('役割決め', [
        ['explanation' => 'あなたは「買い手」に決まりました。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたはある抽象的な財を1単位購入したいと考えています。'],
        ['explanation' => 'あなたがこの財に支払っても良いと考えている最大の価格は'. $_con->get_personal('money', 0) .'円です。'],
        ['explanation_sub' => 'もしあなたが'. $_con->get_personal('money', 0) .'円よりも低い価格でこの財を購入したら、思っていた価格より安いので、得をしたことになります。'],
        ['explanation_sub' => '逆に、もしあなたが'. $_con->get_personal('money', 0) .'円よりも高い価格でこの財を購入したら、思っていた価格より高いので、損をしたことになります。'],
        ['explanation' => 'あなたは、なるべく安い価格を売り手に提案することで、得を大きくすることができます。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'なるべく得を大きくすることがあなたの目的です。'],
        ['explanation' => 'ですから、損をしてまで買う必要はありません。'],
        ['explanation_sub' => 'もし購入しなければ、あなたの利潤はゼロですが、マイナスにはなりません。'],
        ['explanation_sub' => 'もし支払っても良いと考えている価格よりも高い額を支払ってしまったら、利潤はマイナスになります。'],
        ['explanation_sub' => '(なお、この実験では支払い意欲よりも高い金額は入力できません)'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたの利潤は次のように計算されます。'],
        ['explanation_sub' => '利潤 = 支払意欲 - 支払価格'],
        ['explanation_sub' => '支払意欲は一定ですので、なるべく安い価格で購入することで利潤を増やすことができます。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたの取引画面には、'],
        ['explanation_sub' => '買い手の買値希望額一覧(あなたを含む)'],
        ['explanation_sub' => '売り手の売値希望額一覧'],
        ['explanation' => 'が表示されます。'],
        ['explanation_blank' => ''],
        ['explanation' => '取引成立済み価格一覧をみながら価格を決めても構いません'],
    ])->add_page('待機', [
        ['explanation' => 'それでは実験開始までしばらくお待ち下さい。'],
    ]);
    break;
case 'seller':
    $explanation
      ->add_page('役割決め', [
        ['explanation' => 'これからコンピュータがみなさんを「売り手」と「買い手」の2組に分けます。'],
        ['explanation' => '「次へ」ボタンを押して役割を確認してください。'],
    ])->add_page('役割決め', [
        ['explanation' => 'あなたは「売り手」に決まりました。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたはある抽象的な財を1単位販売したいと考えています。'],
        ['explanation' => 'あなたはこの財を手に入れるために'. $_con->get_personal('cost', 0) .'円支払わなければなりません。'],
        ['explanation_sub' => 'もしあなたが'. $_con->get_personal('cost', 0) .'円よりも高い価格でこの財を販売したら、仕入価格より高いので、得をしたことになります。'],
        ['explanation_sub' => '逆に、もしあなたが'. $_con->get_personal('cost', 0) .'円よりも低い価格でこの財を販売したら、仕入価格より低いので、損をしたことになります。'],
        ['explanation' => 'あなたは、なるべく高い価格を買い手に提案することで、得を大きくすることができます。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'なるべく得を大きくすることがあなたの目的です。'],
        ['explanation' => 'ですから、損をしてまで売る必要はありません。'],
        ['explanation_sub' => 'もし販売しなければ、あなたの利潤はゼロですが、マイナスにはなりません。'],
        ['explanation_sub' => 'もし仕入た価格よりも低い額で販売してしまったら、利潤はマイナスになります。'],
        ['explanation_sub' => '(なお、この実験では仕入値よりも低い金額は入力できません)'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたの利潤は次のように計算されます。'],
        ['explanation_sub' => '利潤 = 販売価格 - 仕入価格'],
        ['explanation_sub' => '仕入価格は一定ですので、なるべく高い価格で販売することで利潤を増やすことができます。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'あなたの取引画面には、'],
        ['explanation_sub' => '買い手の買値希望額一覧'],
        ['explanation_sub' => '売り手の売値希望額一覧(あなたを含む)'],
        ['explanation' => 'が表示されます。'],
        ['explanation_blank' => ''],
        ['explanation' => '取引成立済み価格一覧をみながら価格を決めても構いません'],
    ])->add_page('待機', [
        ['explanation' => 'それでは実験開始までしばらくお待ち下さい。'],
    ]);
    break;
}

// 実験画面
$page_container->add_page('experiment', $experiment = new NormalContainer());
$experiment->add(new TemplateUI($templates['experiment']['status'], function () use ($_con) {
    return [
        'role' => $_con->get_personal('role', ''),
        'money' => $_con->get_personal('money', 0),
        'cost' => $_con->get_personal('cost', 0),
        'allow_loss' => $_con->get('allow_loss', false),
        'tax_buy' => $_con->get('tax_buy', false),
        'tax_sell' => $_con->get('tax_sell', false),
    ];
}));
$experiment->add(new StaticUI($templates['experiment']['input']));
$_con->set_personal('caution', false);
$experiment->add(new SendingUI('提案', function ($value) use ($_con) {
    if ($_con->get_personal('finished', false)) return;
    $value = intval($value);
    if ($value <= 0) {
        $_con->set_personal('caution', '0以下の値は入力できません。');
        return;
    }
    // tax (prepared)
    // allow_loss
    if (!$_con->get('allow_loss', false)) {
        switch ($_con->get_personal('role', '')) {
        case 'buyer':
            if ($value > $_con->get_personal('money', 0)) {
                $_con->set_personal('caution', '損失が発生するためキャンセルされました。');
                return;
            }
            break;
        case 'seller':
            if ($value < $_con->get_personal('cost', 0)) {
                $_con->set_personal('caution', '損失が発生するためキャンセルされました。');
                return;
            }
            break;
        }
    }
    $_con->set_personal('caution', '価格を提示しました。');
    try {
        $_con->lock();
        // set price
        $_con->set_personal('price', $value);
        $_con->set_personal('ask_time', microtime(true));
        // trade
        switch ($_con->get_personal('role', '')) {
        case 'seller':
            // trade check
            $list = [];
            $sort_arr = ['price' => [], 'time' => []];
            foreach ($_con->participants as $participant) {
                if (($_con->get_personal('finished', false, $participant['id']))
                    || ($_con->get_personal('role', '', $participant['id']) != 'buyer')
                    || (($price = $_con->get_personal('price', 0, $participant['id'])) <= 0)
                ) continue;
                $list[] = [
                    'id' => $participant['id'],
                    'price' => $price,
                    'time' => $_con->get_personal('ask_time', 0, $participant['id']),
                ];
            }
            if ($list == []) return;
            $prices = [];
            $times = [];
            foreach ($list as $key => $val) {
                $prices[$key] = $val['price'];
                $times[$key] = $val['time'];
            }
            array_multisort($prices, SORT_DESC, SORT_NUMERIC,
                              $times, SORT_ASC, SORT_NUMERIC,
                              $list);
            if (!($value <= current($list)['price'])) return;
            // success
            $buyer = current($list)['id'];
            $seller = $_con->participant['id'];
            break;
        case 'buyer':
            // trade check
            $list = [];
            foreach ($_con->participants as $participant) {
                if (($_con->get_personal('finished', false, $participant['id']))
                    || ($_con->get_personal('role', '', $participant['id']) != 'seller')
                    || (($price = $_con->get_personal('price', 0, $participant['id'])) <= 0)
                ) continue;
                $list[] = [
                    'id' => $participant['id'],
                    'price' => $price,
                    'time' => $_con->get_personal('ask_time', 0, $participant['id']),
                ];
            }
            if ($list == []) return;
            $prices = [];
            $times = [];
            foreach ($list as $key => $val) {
                $prices[$key] = $val['price'];
                $times[$key] = $val['time'];
            }
            array_multisort($prices, SORT_ASC, SORT_NUMERIC,
                              $times, SORT_ASC, SORT_NUMERIC,
                              $list);
            if (!($value >= current($list)['price'])) return;
            // success
            $buyer = $_con->participant['id'];
            $seller = current($list)['id'];
            break;
        }
        // success
        $_con->set_personal('finished', true, $buyer);
        $_con->set_personal('finished', true, $seller);
        $_con->set_personal('page', 'finished', $buyer);
        $_con->set_personal('page', 'finished', $seller);
        $_con->set_personal('price', $value, $buyer);
        $_con->set_personal('price', $value, $seller);
        $_con->set_personal('profit', $_con->get_personal('money', 0, $buyer) - $value, $buyer);
        $_con->set_personal('profit', $value - $_con->get_personal('cost', 0, $seller), $seller);
    } finally {
        $_con->unlock();
    }
}));
$experiment->add(new ButtonUI($_con,
    function ($_con) { return '取り消し'; },
    function ($_con) {
        $_con->set_personal('caution', '価格の提示を取り下げました。');
        $_con->lock();
            if (!$_con->get_personal('finished', false))
                $_con->set_personal('price', null);
        $_con->unlock();
    }
));
$experiment->add(new TemplateUI($templates['experiment']['caution'], function () use ($_con) {
    return [
        'caution' => $_con->get_personal('caution', false)
    ];
}));
$_con->set_personal('caution', false);

$experiment_list = new TemplateUI($templates['experiment']['list'], function () use ($_con) {
    $buy_list = [];
    $sell_list = [];
    $finished_list = [];
    $_con->lock();
        foreach ($_con->participants as $participant) {
            $price = $_con->get_personal('price', 0, $participant['id']);
            if ($price <= 0) continue;
            if (!$_con->get_personal('finished', false, $participant['id']))
                switch ($_con->get_personal('role', '', $participant['id'])) {
                case 'seller':
                    $sell_list[$participant['id']] = $price;
                    break;
                case 'buyer':
                    $buy_list[$participant['id']] = $price;
                    break;
                }
            else
                if ($_con->get_personal('role', '', $participant['id']) == $_con->get_personal('role', '', $_con->participant['id']))
                    $finished_list[$participant['id']] = $price;
        }
    $_con->unlock();
    arsort($buy_list);
    asort($sell_list);

    $list = [
        'buy' => ['head' => '買値', 'body' => []],
        'sell' => ['head' => '売値', 'body' => []],
        'finished' => ['head' => '成立価格', 'body' => []],
    ];
    foreach ($buy_list as $id => $price)
        $list['buy']['body'][] = ['price' => $price] + (($id == $_con->participant['id'])? ['self' => true]: []);
    foreach ($sell_list as $id => $price)
        $list['sell']['body'][] = ['price' => $price] + (($id == $_con->participant['id'])? ['self' => true]: []);
    foreach ($finished_list as $id => $price)
        $list['finished']['body'][] = ['price' => $price] + (($id == $_con->participant['id'])? ['self' => true]: []);
    return ['list' => [$list['buy'], $list['sell'], $list['finished']], 'update' => (count($list['buy']) + count($list['sell']) + count($list['finished']))];
});
$experiment->add($experiment_list);

// 終了画面
$page_container->add_page('finished', $finished = new NormalContainer());
$finished->add(new TemplateUI($templates['finished'], function () use ($_con) {
    return [
        'role' => $_con->get_personal('role', ''),
        'money' => $_con->get_personal('money', 0),
        'cost' => $_con->get_personal('cost', 0),
        'price' => $_con->get_personal('price', 0),
        'profit' => $_con->get_personal('profit', 0),
    ];
}));
$finished->add($experiment_list);

// 結果画面
$page_container->add_page('result', $result = new NormalContainer());
$result->add(new TemplateUI($templates['result']['personal'], function () use ($_con) {
    return [
        'role' => $_con->get_personal('role', ''),
        'money' => $_con->get_personal('money', 0),
        'cost' => $_con->get_personal('cost', 0),
        'price' => $_con->get_personal('price', 0),
        'profit' => $_con->get_personal('profit', 0),
    ];
}));
$result->add(new TemplateUI($templates['result']['ranking'],
    function () use ($_con) {
        $profits = [];
        foreach ($_con->participants as $participant)
            if ($_con->get_personal('finished', false, $participant['id']))
                $profits[$participant['id']] = $_con->get_personal('profit', 0, $participant['id']);
        arsort($profits);
        $rank = 1;
        $ranking = [];
        $foot = [];
        $result_profit = 0;
        $result_price = 0;
        foreach ($profits as $id => $profit) {
            $ranking[] = [
                'no' => $rank++,
                'role' => $_con->get_personal('role', '', $id),
                'money' => $_con->get_personal('money', 0, $id),
                'cost' => $_con->get_personal('cost', 0, $id),
                'price' => $_con->get_personal('price', 0, $id),
                'profit' => $profit,
            ] + (($id == $_con->participant['id'])? ['self' => true]: []);
            $result_profit += $profit;
            $result_price += $_con->get_personal('price', 0, $id);
        }
        $n = $_con->get('join_num', 2);
        $n -= $n%2;
        $foot['profit'][] = ['theorical' => (100 * $n*$n/4), 'result' => $result_profit];
        $foot['price'][] = ['theorical' => (100 * (1 + $n)/2), 'result' => round($result_price / count($profits))];

        return ['ranking' => $ranking, 'profit' => $foot['profit'], 'price' => $foot['price']];
    }, function () {
        return ['event' => <<<'JS'
function(selector, update){
    $('.tablesorter').tablesorter();
}
JS
        ];
    }
));
$result->add(new StaticUI($templates['result']['graph']));
$result->add(new ScatterGraph(
    call_user_func(function () use ($_con) {
        $supply = [];
        $demand = [];
        foreach ($_con->participants as $participant) {
            switch($_con->get_personal('role', '', $participant['id'])) {
            case 'seller':
                $supply[] = $_con->get_personal('cost', 0, $participant['id']);
                break;
            case 'buyer':
                $demand[] = $_con->get_personal('money', 0, $participant['id']);
                break;
            }
        }

        $data = [
            'supply' => [
                'values' => [],
                'color' => 'green',
            ],
            'demand' => [
                'values' => [],
                'color' => 'red',
            ],
        ];
        asort($supply);
        arsort($demand);
        $i = 1;
        foreach ($supply as $val)
            $data['supply']['values'][] = ['x' => $i++, 'y' => $val];
        $i = 1;
        foreach ($demand as $val)
            $data['demand']['values'][] = ['x' => $i++, 'y' => $val];
        return $data;
    }), ['label' => ['x' => '', 'y' => '価格']]
));
