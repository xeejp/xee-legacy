<?php

$_loader = new ClassLoader();
$_loader->register_directory(__DIR__ . '/com');
$_loader->register();

// page settings
$pages = [];
$pages['reject'] = new RedirectUI(_URL, $_con->get_personal('page', 'wait') == 'reject');
$pages['wait']   = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages['experiment'] = new NormalContainer();
$pages['result'] = new TemplateUI(<<<TMPL
最終利得 : {score}円
TMPL
, ['score' => $_con->get_personal('money') - $_con->get_personal('cost')]);

// 実験画面
switch ($_con->get_personal('role')) {
case 'seller':
    $page_head = new TemplateUI(<<<'TMPL'
あなたは{role}です<br/>
仕入値 : {cost}円<br/>
TMPL
,   ['role' => '売り手', 'cost' => $_con->get_personal('cost')]);
    break;
case 'buyer':
    $page_head = new TemplateUI(<<<'TMPL'
あなたは{role}です<br/>
所持金 : {money}円<br/>
TMPL
,   ['role' => '買い手', 'money' => $_con->get_personal('money')]);
    break;
default:
    $page_head = new StaticUI('');
}

$page_list = new TemplateUI(<<<TMPL
販売価格<br/>
{each sell_list}
<span>売値 : {price} 円</span><br/>
{/each}
買取価格<br/>
{each buy_list}
<span>買値 : {price} 円</span><br/>
{/each}
TMPL
,   call_user_func(function($con){
        $sell_list = [];
        $buy_list = [];
        foreach ($con->participants as $participant) {
            if (($price = $con->get_personal('price', 0, $participant['id'])) <= 0)
                continue;
            switch ($con->get_personal('role', null, $participant['id'])) {
            case 'seller':
                $sell_list[] = ['price' => $price];
                break;
            case 'buyer':
                $buy_list[] = ['price' => $price];
                break;
            }
        }
        return ['sell_list' => $sell_list, 'buy_list' => $buy_list];
    }, $_con)
);

$page_form = new NormalContainer();
$page_form->add(new SendingUI('決定', function($value)use($_con){
    if (($price = intval($value)) <= 0) return;
    if ($_con->get('allow_loss', 'false') != 'true') {
        switch($_con->get_personal('role')) {
        case 'seller':
            if ($price < $_con->get_personal('cost'))
                return;
            break;
        case 'buyer':
            if ($price > $_con->get_personal('money'))
                return;
            break;
        }
    }
    $_con->set_personal('price', $price);
    // trade
    $market = [];
    foreach ($_con->participants as $participant) {
        if ($_con->get_personal('role') == $_con->get_personal('role', null, $participant['id'])
                || ($value = $_con->get_personal('price', 0, $participant['id'])) <= 0)
            continue;
        $market[$participant['id']] = $value;
    }
    if ($market == []) return;
    // success
    switch($_con->get_personal('role')) {
    case 'seller':
        arsort($market);
        if (($value = current($market)) < $price) return;
        $id = key($market);
        $_con->set_personal('price', 0, $id);
        $_con->set_personal('money', $_con->get_personal('money', 0, $id) - $value, $id);
        $_con->set_personal('finish', true, $id);
        $_con->set_personal('price', 0);
        $_con->set_personal('money', $_con->get_personal('money') + $value);
        $_con->set_personal('finish', true);
        break;
    case 'buyer':
        asort($market);
        if ($value = current($market) > $price) return;
        $id = key($market);
        $_con->set_personal('price', 0);
        $_con->set_personal('money', $_con->get_personal('money', 0, $id) - $price);
        $_con->set_personal('finish', true);
        $_con->set_personal('price', 0, $id);
        $_con->set_personal('money', $_con->get_personal('money') + $price, $id);
        $_con->set_personal('finish', true, $id);
        break;
    }
    $_con->set_personal('page', 'result');
    $_con->set_personal('page', 'result', $id);
}));
$page_form->add(new EventButton('キャンセル'), function()use($_con){
    $_con->set_personal('price', 0);
});

$pages['experiment']->add($page_head);
$pages['experiment']->add($page_list);
$pages['experiment']->add($page_form);

// add pages
$_con->add_component($_page = new PageContainer($_con->get_personal('page', 'wait')));
foreach ($pages as $key => $value) {
    $_page->add($key, $value);
}

