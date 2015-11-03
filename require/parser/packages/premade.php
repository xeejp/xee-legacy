<?php
// package: premade
$_con = $this->get_controller();
return [
    'double_auction' => function () use ($_con) {
        // page settings
        $pages = [];
        $pages['reject'] = new RedirectUI(_URL, $_con->get_personal('page', 'wait') == 'reject');
        $pages['wait']   = new StaticUI('<br/><br/><center>Waiting now</center>');
        $pages['experiment'] = new NormalContainer();
        $pages['result'] = new TemplateUI(<<<TMPL
Your profit : \${score}<br/>
TMPL
        , ['score' => $_con->get_personal('money', 0) - $_con->get_personal('cost', 0)]);

        // 実験画面
        switch ($_con->get_personal('role')) {
        case 'seller':
            $page_head = new TemplateUI(<<<'TMPL'
Now, we will play market eauiriblium experiment.<br/>
You play the role of {role}.<br/>
Your cost of property is ${cost}.<br/>
Tax is ${tax}.<br/>
TMPL
        ,   ['role' => 'Seller', 'cost' => $_con->get_personal('cost'), 'tax' => $_con->get('tax', 0)]);
            break;
        case 'buyer':
            $page_head = new TemplateUI(<<<TMPL
Now, we will play market eauiriblium experiment.<br/>
You play the role of {role}.<br/>
Your price of willingness to pay is up to \${money}.<br/>
Tax is \${tax}.<br/>
TMPL
        ,   ['role' => 'Buyer', 'money' => $_con->get_personal('money'), 'tax' => $_con->get('tax', 0)]);
            break;
        default:
            $page_head = new StaticUI('');
        }

        $page_list = new TemplateUI(<<<TMPL
{if sell_list}
Sellers want to sell you the prices as follows:<br/>
{/if}
{each sell_list}
<span>Selling price : \${price}</span><br/>
{/each}
{if buy_list}
The other buyers want to buy the prices as follows:<br/>
{/if}
{each buy_list}
<span>Buying price : \${price}</span><br/>
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
        $page_form->add(new StaticUI('Please enter your price to buy.<br/>'));
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
                $_con->set_personal('price', 0, $id);
                $_con->set_personal('money', $_con->get_personal('money', 0, $id) - $value, $id);
                $_con->set_personal('finish', true, $id);
                $_con->set_personal('price', 0);
                $_con->set_personal('money', $_con->get_personal('money', 0) + $value - $tax);
                $_con->set_personal('finish', true);
                break;
            case 'buyer':
                asort($market);
                if ($value = current($market) > $price) return;
                $id = key($market);
                $_con->set_personal('price', 0);
                $_con->set_personal('money', $_con->get_personal('money', 0) - $price);
                $_con->set_personal('finish', true);
                $_con->set_personal('price', 0, $id);
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
        $pages['experiment']->add($page_form);

        // add pages
        $_page = new PageContainer($_con->get_personal('page', 'wait'));
        foreach ($pages as $key => $value) {
            $_page->add_page($key, $value);
        }

        $_con->add_component(new StaticUI('<div><div style="margin: 0 auto; width: 25em;">'));
        $_con->add_component($_page);
        $_con->add_component(new StaticUI('</div></div>'));
    },
];
