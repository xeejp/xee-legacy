<?php

// page settings
$pages = [];
$pages['reject'] = new RedirectUI(_URL, $_con->get_personal('page', 'wait') == 'reject');
$pages['wait']   = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages['experiment'] = new NormalContainer();
$pages['result'] = new TemplateUI(<<<'TMPL'
Your role : ${role}<br/>
Your money : ${money}<br/>
{if cost}Your cost : ${cost}<br/>{/if}
{if pay}Your pay : ${pay}<br/>{/if}
Your profit : ${score}<br/>
TMPL
, function () use ($_con) {
    return array_merge(
        [
            'role' => $_con->get_personal('role'),
            'money' => $_con->get_personal('money', 0),
            'score' => $_con->get_personal('money', 0) - $_con->get_personal('cost', 0)
        ],
        ($_con->get_personal('role') == 'seller')? ['cost' => $_con->get_personal('cost', 0)]: [],
        ($_con->get_personal('role') == 'buyer')? [
            'money' => $_con->get_personal('money', 0) + $_con->get_personal('pay', 0),
            'pay' => $_con->get_personal('pay', 0)
        ]: []
    );
});

// 実験画面
$page_head = new TemplateUI(<<<'TMPL'
Now, we will play market equilibrium experiment.<br />
You play the role of <b>{role}</b>.<br/>
{if buyer}Your price of willingness to pay is up to ${money}.<br/>{/if}
{if seller}Your cost price is ${cost}.<br/>{/if}
{if tax}Tax is ${tax}<br/>{/if}
<br/>
{if buyer}
If you buy over ${money}, then you lose profits
<center><i>buying price - ${money}</i>.</center>
If you buy under ${money}, then you gain profits<center><i>${money} - buying price</i>.</center>
{/if}
{if seller}
If you sell under ${cost}, then you lose profits<center><i>${cost} - selling price</i>.</center>
If you sell over ${cost}, then you gain profits<center><i>selling price - ${cost}</i>.</center>
{/if}
TMPL
, function () use ($_con) {
    return array_merge(
        ($_con->get_personal('role') == 'buyer')? ['buyer' => true, 'role' => 'Buyer']
            : (($_con->get_personal('role') == 'seller')? ['seller' => true, 'role' => 'Seller']: []),
        ['money' => $_con->get_personal('money', 0), 'cost' => $_con->get_personal('cost', 0)],
        (intval($_con->get('tax', 0)) > 0)? ['tax' => intval($_con->get('tax', 0))]: []
    );
});

$page_list = new TemplateUI(<<<'TMPL'
<hr/>
{if buy_list}
Buyers want to buy at the following prices:<br/>
<div style="width: 400px; padding: 5px; background-color:lightgray; border:1px solid black;">
{each buy_list}
<span>Buying price: ${price}</span><br/>
{/each}
</div>
<br/>
{/if}
{if sell_list}
Sellers want to sell at the following prices:<br/>
<div style="width: 400px; padding: 5px; background-color:lightgray; border:1px solid black;">
{each sell_list}
<span>Selling price: ${price}</span><br/>
{/each}
</div>
<hr/><br/>
{/if}

TMPL
, function () use ($_con) {
    $sell_list = [];
    $buy_list = [];
    foreach ($_con->participants as $participant) {
        if ( (($price = $_con->get_personal('price', 0, $participant['id'])) <= 0)
                || ($_con->get_personal('finished', false, $participant['id']))
            ) continue;
        switch ($_con->get_personal('role', null, $participant['id'])) {
        case 'seller':
            $sell_list[] = ['price' => $price];
            break;
        case 'buyer':
            $buy_list[] = ['price' => $price];
            break;
        }
    }
    return ['sell_list' => $sell_list, 'buy_list' => $buy_list];
});


$page_finished = new TemplateUI(<<<'TMPL'
<hr/>
{if finished_list}
Other finished players prices:<br/>
<div style="width: 400px; padding: 5px; background-color:lightgray; border:1px solid black;">
{each finished_list}
<span>Price: ${price}</span><br/>
{/each}
</div>
<hr/><br/>
{/if}

TMPL
, function () use ($_con) {
    $finished_list = [];
    foreach ($_con->participants as $participant) {
        if (!$_con->get_personal('finish', false, $participant['id'])) continue;
        if ($_con->get_personal('role', null, $participant['id']) == 'seller')
            $finished_list[] = ['price' => $_con->get_personal('price', 0, $participant['id'])];
    }
    asort($finished_list);
    return ['finished_list' => $finished_list];
});

$page_form = new NormalContainer();
$page_form->add(new StaticUI('Please enter your price to propose.<br/>'));
$page_form->add(new SendingUI('Submit', function($value)use($_con){
    $tax = $_con->get('tax', 0);
    $price = intval($value);
    $price = ($_con->get_personal('role') == 'seller')? $price + $tax: $price - $tax;
    if ($price <= 0) return;
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
        $_con->set_personal('pay', $value, $id);
        $_con->set_personal('money', $_con->get_personal('money', 0, $id) - $value, $id);
        $_con->set_personal('finish', true, $id);
        $_con->set_personal('money', $_con->get_personal('money', 0) + $value - $tax);
        $_con->set_personal('finish', true);
        break;
    case 'buyer':
        asort($market);
        if ($value = current($market) > $price) return;
        $id = key($market);
        $_con->set_personal('pay', $price);
        $_con->set_personal('money', $_con->get_personal('money', 0) - $price);
        $_con->set_personal('finish', true);
        $_con->set_personal('money', $_con->get_personal('money', 0, $id) + $price - $tax, $id);
        $_con->set_personal('finish', true, $id);
        break;
    }
    $_con->set_personal('page', 'result');
    $_con->set_personal('page', 'result', $id);
}));
$page_form->add(new ButtonUI($_con,
    function($_con){ return 'Cancel'; },
    function($_con){ $_con->set_personal('price', 0); }
));

$pages['experiment']->add($page_head);
$pages['experiment']->add($page_list);
$pages['experiment']->add($page_finished);
$pages['experiment']->add($page_form);

// add pages
$_page = new PageContainer(function () use ($_con) {
    return $_con->get_personal('page', 'wait');
});
foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

$_con->add_component(new StaticUI('<div><div style="margin: 0 auto; width: 25em;">'));
$_con->add_component($_page);
$_con->add_component(new StaticUI('</div></div>'));
