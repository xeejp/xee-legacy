<?php

require 'common.php';

// page settings
$pages = [];
$pages[PAGE_REJECT]             = new RedirectUI(_URL, $_con->get_personal(VAR_PAGE, PAGE_WAIT) == PAGE_REJECT);
$pages[PAGE_WAIT]               = new StaticUI('<br/><br/><center>Waiting now</center>');
$pages[PAGE_EXPLANATION]        = new ExplanationUI($_con);
$pages[PAGE_EXPERIMENT]         = new NormalContainer();
$pages[PAGE_PUNISHMENT]         = new NormalContainer();
$pages[PAGE_WAIT_ACTION]        = new NormalContainer();
$pages[PAGE_PUNISHMENT_RESULT]  = new NormalContainer();
$pages[PAGE_MIDDLE_RESULT]      = new NormalContainer();
$pages[PAGE_FINAL_RESULT]       = new NormalContainer(); 
$pages[PAGE_GRAPH]              = new NormalContainer(); 


$pages[PAGE_EXPLANATION]->add_page('グループ分け', [
        ['explanation' => 'これからコンピュータがみなさんをランダムに4人1組のグループへ振り分けます。'],
        ['explanation' => '「次へ」ボタンを押して実験相手を確認してください。'],
    ])->add_page('役割決め',
        call_user_func(
            function($con) {
                $exp = [];
                $exp[] = ['explanation' => 'あなたの実験相手が決まりました。'];
                $counter = 0;
                foreach ( $con->participants as $participant ) {
                    ++$counter;
                    $id     = strval($participant[VAR_ID]);
                    $suffix = '';
                    if ( isCurrentUser($con, $id) ) {
                        $suffix =  '(あなた)';
                    }

                    $exp[] = ['explanation_sub' => strval($counter) . '人目のメンバーのID：' . $id . $suffix];
                }

                return $exp;
            }
            ,$_con
        )
    )->add_page('ルール説明', [
        ['explanation' => 'グループ内の各メンバーは、それぞれ20ポイントずつ持っています。'],
        ['explanation' => '各メンバーは、自分の所持しているポイントの中から一部または全部をグループで実施するプロジェクトのために投資することができます。'],
        ['explanation_sub' => 'まったく投資しないこともできます。その場合は0を入力してください。'],
        ['explanation_sub' => '投資せずに残ったポイントは、そのまま自分のポイントになります。'],
        ['explanation' => 'あなたは、20ポイントをそのまま持っておく分と、プロジェクトに投資する分に分けてください。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'グループの各メンバーが投資額を決定したら、グループ全員の投資額を合計します。'],
        ['explanation' => '投資額合計を0.4倍したポイントがグループ内のメンバー全員に配られます。'],
        // MEMO: 修正して小数点以下の数まで扱えるようになりました['explanation_sub' => '小数点以下切り捨てで整数で配られます。'],
        ['explanation' => 'つまり、あなたの利益は次のように計算されます。'],
        ['explanation_sub' => 'あなたの利益＝20pt−あなたの投資額＋(0.4×グループ全員の投資額合計)'],
        // MEMO: 「入力できます」の間違い?['explanation_sub' => '(なお、この実験では20pt以下の正の整数のみ入力できません。)'],
        ['explanation_sub' => '(なお、この実験では20pt以下の正の整数のみ入力できます。)'],
    ])->add_page('ルール説明(投資例)', [
        ['explanation' => 'あなたの利益＝20pt−あなたの投資額＋(0.4×グループ全員の投資額合計)'],
        ['explanation_sub' => 'どのメンバーも投資しない場合：あなたの投資額は0、グループ全員の投資額合計は0。'],
        ['explanation_sub' => 'つまり、あなたの利益は20ptになります。'],
        ['explanation_sub' => '全メンバーが半分の10ptを投資する場合：あなたの投資額は10、グループ全員の投資額合計は40。'],
        ['explanation_sub' => 'つまり、あなたの利益は26ptになります。'],
        ['explanation_sub' => '全メンバーが20pt全部を投資する場合：あなたの投資額は20、グループ全員の投資額合計は80。'],
        ['explanation_sub' => 'つまり、あなたの利益は32ptになります。'],
    ])->add_page('ルール説明', [
        // MEMO: 罰ありとなしとで記述を分けたほうがよい？['explanation' => 'この投資をメンバーを変えずに'. strval(intval($_con->get(VAR_TURN_NO_PUNISH, 0)) + intval($_con->get(VAR_TURN_PUNISH, 0))) .'ターン繰り返します。'],
        ['explanation' => 'この投資をメンバーを変えずに'. strval(intval($_con->get(VAR_TURN_NO_PUNISH, 0))) .'ターン繰り返します。'],
        ['explanation_sub' => '投資できる最大額は毎ターン20ポイントです。'],
        ['explanation_sub' => '各ターンで得られた利益は、累積されてページ上部に表示されます。'],
        ['explanation_sub' => '各ターン毎に、他のユーザーの投資額が表示されます。'],
        ['explanation_blank' => ''],
        ['explanation' => 'あなたは、なるべく総利益が最大になるように投資してください。'],
    ])->add_page('待機', [
        ['explanation' => 'それでは実験開始までしばらくお待ち下さい。'],
    ]);

$pages[PAGE_EXPERIMENT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
You have {cur_pt} points.<br/>
And, your sum of profit is {total_profit} points.<br/>
What point do you invest?<br/>
TMPL
,   function()use($_con) {
        return [
            'turn'          => $_con->get(VAR_TURN, 0),
            'id'            => $_con->get_personal(VAR_CUR_ID, 0),
            'cur_pt'        => $_con->get_personal(VAR_CUR_PT, 0),
            'total_profit'  => $_con->get_personal(VAR_TOTAL_PROFIT, 0)
        ];
    }
));

$pages[PAGE_EXPERIMENT]->add(new SendingUI('invest', 
    function($value)use($_con) {
        $invest_pt  = intval($value);
        $cur_pt     = $_con->get_personal(VAR_CUR_PT, 0);
        if ( !isValidValue($invest_pt, 0, $cur_pt) ) {
            return;
        }

        $_con->set_personal(VAR_INVEST_PT, $invest_pt);
        appendInvestmentData($_con, $invest_pt);

        $_con->set_personal(VAR_READY, true); 
        if ( isReady(calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_MIDDLE_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_PUNISHMENT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
You have {punish_pt} points for punishment.<br/>
What point do you use for punishment.<br/><br/>
TMPL
,   function()use($_con) {
        return [
            'turn'          => $_con->get(VAR_TURN, 0), 
            'id'            => $_con->get_personal(VAR_CUR_ID, 0), 
            'punish_pt'     => $_con->get_personal(VAR_CUR_PUNISH_PT, 0), 
        ]; 
    }
));

$pages[PAGE_PUNISHMENT]->add(new MultiSendingUI('OK',
    call_user_func(
        function($con) {
            $list = [];
            foreach ( $con->participants as $participant ) {
                $id = $participant[VAR_ID];
                if ( isCurrentUser($con, $id) ) {
                    $cur_id = $con->get_personal(VAR_CUR_ID, 0);
                    continue;
                }

                $invest_pt      = $con->get_personal(VAR_INVEST_PT, 0, strval($id));
                $description    = 'ID:' . $id . ' Investment point:' . $invest_pt . ' ';
                $list[] = [
                    'id'            => $id,
                    'description'   => $description,
                ];
            }

            return $list;
        }, 
        $_con
    ),
    function($value)use($_con) {
        dump('[index.php new MultiSendingUI] sending is called.', true);
        dump('[index.php new MultiSendingUI] value:' . dump($value), true);

        $total_punish   = calcTotalPunishment($value);
        $cur_punish_pt  = $_con->get_personal(VAR_CUR_PUNISH_PT, 0);
        if ( !isValidValue($total_punish, 0, $cur_punish_pt) ) {
            return;
        }
        $_con->set_personal(VAR_PUNISH_PT, $total_punish);

        foreach ( $value as $id => $punish_pt ) {
            if ( isCurrentUser($_con, $id) ) {
                continue;
            } 

            calcReceivedPunishment($_con, $id, $punish_pt);
        }

        $_con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_PUNISHMENT_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_WAIT_ACTION]->add(new TemplateUI(<<<TMPL
Waiting for {num_not_ready_user} users...<br/>
TMPL
,   function()use($_con) { 
        $num_not_ready_user = NUM_PLAYER - calcNumReadyUser($_con);

        return [
            'num_not_ready_user'    => $num_not_ready_user
        ];
    }
));


$pages[PAGE_PUNISHMENT_RESULT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
Punishment Result<br/>
{each punish_list}
<span>ID:{id} Recieved Punishment Point:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $turn           = $_con->get(VAR_TURN, 0);
        $punish_list    = [];
        foreach ( $_con->participants as $participant ) {
            $id             = $participant[VAR_ID];
            $pt             = $_con->get_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id));
            $punish_list[]  = ['id' => $id, 'pt' => $pt];
        } 

        return [
            'turn'          => $turn,
            'id'            => $_con->get_personal(VAR_CUR_ID, 0),
            'punish_list'   => $punish_list
        ];
    }
));

$pages[PAGE_PUNISHMENT_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        reduceTotalProfit($con);

        $con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($con)) ) {
            $turn = inclementTurn($con);
            if ( isFinishCurrentPhase($con, $turn) ) {
                redirectAllUsers($con, PAGE_FINAL_RESULT); 
            } else {
                initAllUsersData($con);
                redirectAllUsers($con, PAGE_EXPERIMENT);
            } 
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_MIDDLE_RESULT]->add(new TemplateUI(<<<TMPL
Turn:{turn}<br/>
Your ID:{id}<br/>
Middle Result<br/>
{each invest_list}
<span>ID:{id} Investment Point:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $turn           = $_con->get(VAR_TURN, 1);
        $invest_list    = [];
        foreach ( $_con->participants as $participant ) {
            $id             = $participant[VAR_ID];
            $pt             = $_con->get_personal(VAR_INVEST_PT, 0, strval($id));
            $invest_list[]  = [VAR_ID => $id, 'pt' => $pt];
        } 

        return [
            'turn'          => $turn,
            'id'            => $_con->get_personal(VAR_CUR_ID, 0),
            'invest_list'   => $invest_list
        ];
    }
));

$pages[PAGE_MIDDLE_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) {
        setTotalProfit($con);

        $con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($con)) ) {
            if ( isPunishPhase($con) ) {
                redirectAllUsers($con, PAGE_PUNISHMENT);
            } else {
                $turn = inclementTurn($con);
                if ( isFinishCurrentPhase($con, $turn) ) {
                    redirectAllUsers($con, PAGE_FINAL_RESULT); 
                } else {
                    initAllUsersData($con);
                    redirectAllUsers($con, PAGE_EXPERIMENT);
                } 
            }
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_FINAL_RESULT]->add(new TemplateUI(<<<TMPL
Final Result<br/>
Your ID:{id}<br/>
{each total_profit_list}
<span>ID:{id} Total Profit:{pt}</span><br/>
{/each}
TMPL
,   function()use($_con) {
        $total_profit_list = [];
        foreach ( $_con->participants as $participant ) {
            $id                     = $participant[VAR_ID];
            $pt                     = $_con->get_personal(VAR_TOTAL_PROFIT, 0, strval($id));
            $total_profit_list[]    = ['id' => $id, 'pt' => $pt];
        } 

        $total_profit_list = sortProfitList($total_profit_list);
        
        return [
            'id'                => $_con->get_personal(VAR_CUR_ID, 0),
            'total_profit_list' => $total_profit_list
        ];
    }
));

$pages[PAGE_FINAL_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return 'OK';
    },
    function($con) { 
        $con->set_personal(VAR_READY, true);
        if ( isReady(calcNumReadyUser($con)) ) {
            $turn = $con->get(VAR_TURN, 0);
            if ( isFinishAllPhase($con, $turn) ) {
                redirectAllUsers($con, PAGE_GRAPH);
            } else {
                $con->set(VAR_TURN, 1);
                if ( changePhase($con) == 0 ) {
                    redirectAllUsers($con, PAGE_GRAPH);
                } else {
                    initAllUsersData($con);
                    setValueToAllUsers($con, VAR_TOTAL_PROFIT, 0);
                    redirectAllUsers($con, PAGE_EXPERIMENT);
                }
            }
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_GRAPH]->add(new StaticUI('Graph<br/>未実装です┌(^o^ ┐)┐'));

/*
$pages[PAGE_GRAPH]->add(new TemplateUI(<<<TMPL
{each invest_list}{pt}, {/each}
<br/>
TMPL
,   function()use($_con) {
        $invest_array   = splitInvestmentData($_con);
        $invest_list    = [];
        foreach ( $invest_array as $invest_pt ) {
            $invest_list[] = ['pt' => $invest_pt];
        }

        return [
            'invest_list' => $invest_list
        ];
    }
));

$pages[PAGE_GRAPH]->add(new ScatterGraph(
    call_user_func(
        function()use($_con) {
            foreach ($_con->participants as $participant)
    //            if ($_con->get_personal('finished', false, $participant['id']))
                    switch($_con->get_personal('role', '', $participant['id'])) {
                    case 'seller':
                        $supply[] = $_con->get_personal('profit', 0, $participant['id']);
                        break;
                    case 'buyer':
                        $demand[] = $_con->get_personal('profit', 0, $participant['id']);
                        break;
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
        }
    ),
    ['label' => ['x' => '', 'y' => '価格']]
));
*/


// add all pages
$_con->add_component($_page = new PageContainer(
    function()use($_con) { 
        return $_con->get_personal(VAR_PAGE, PAGE_WAIT); 
    }
));

foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

