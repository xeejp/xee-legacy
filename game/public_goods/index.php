<?php

require 'common.php';

// page settings
$pages = [];
$pages[PAGE_REJECT]             = new RedirectUI(_URL, $_con->get_personal(VAR_PAGE, PAGE_WAIT) == PAGE_REJECT);
$_con->add_component(new StaticUI('<div><div style="margin: 0 auto; max-width: 40em;">'));
$pages[PAGE_WAIT]               = new StaticUI('<div style="text-align: center;">
<h1>公共財実験</h1>
<hr/>
実験開始までしばらくお待ちください。</div>');
$pages[PAGE_EXPLANATION]        = new ExplanationUI($_con, 'common');
$pages[PAGE_PUNISH_EXPLANATION] = new NormalContainer();
$pages[PAGE_EXPERIMENT]         = new NormalContainer();
$pages[PAGE_PUNISHMENT]         = new NormalContainer();
$pages[PAGE_WAIT_ACTION]        = new NormalContainer();
$pages[PAGE_PUNISHMENT_RESULT]  = new NormalContainer();
$pages[PAGE_MIDDLE_RESULT]      = new NormalContainer();
$pages[PAGE_FINAL_RESULT]       = new NormalContainer(); 
$pages[PAGE_GRAPH]              = new NormalContainer(); 


$pages[PAGE_EXPLANATION]->add_page('グループ分け', [
        ['explanation' => 'これからコンピュータがみなさんをランダムに'. strval($_con->get(VAR_NUM_PLAYER, 0)) .'人1組のグループへ振り分けます。'],
        ['explanation' => '「次へ」ボタンを押して実験相手を決定してください。'],
    ])->add_page('役割決め', [
        ['explanation' => 'あなたの実験相手が決まりました。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'グループ内の各メンバーは、それぞれ20ポイントずつ持っています。'],
        ['explanation' => '各メンバーは、自分の所持しているポイントの中から一部または全部をグループで実施するプロジェクトのために投資することができます。'],
        ['explanation_sub' => 'まったく投資しないこともできます。その場合は0を入力してください。'],
        ['explanation_sub' => '投資せずに残ったポイントは、そのまま自分のポイントになります。'],
        ['explanation' => 'あなたは、20ポイントをそのまま持っておく分と、プロジェクトに投資する分に分けてください。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'グループの各メンバーが投資額を決定したら、グループ全員の投資額を合計します。'],
        ['explanation' => '投資額合計を0.4倍したポイントがグループ内のメンバー全員に配られます。'],
        ['explanation' => 'つまり、あなたの利益は次のように計算されます。'],
        ['explanation_sub' => 'あなたの利益＝20ポイント−あなたの投資ポイント＋(0.4×グループ全員の合計投資ポイント)'],
        ['explanation_sub' => '(なお、この実験では20pt以下の正の整数のみ入力できます。)'],
    ])->add_page('ルール説明(投資例)', [
        ['explanation' => 'あなたの利益＝20ポイント−あなたの投資ポイント＋(0.4×グループ全員の合計投資ポイント)'],
        ['explanation_sub' => 'どのメンバーも投資しない場合：あなたの投資額は0、グループ全員の投資額合計は0。'],
        ['explanation_sub' => 'つまり、あなたの利益は20ptになります。'],
        ['explanation_sub' => '全メンバーが半分の10ptを投資する場合：あなたの投資額は10、グループ全員の投資額合計は40。'],
        ['explanation_sub' => 'つまり、あなたの利益は26ptになります。'],
        ['explanation_sub' => '全メンバーが20pt全部を投資する場合：あなたの投資額は20、グループ全員の投資額合計は80。'],
        ['explanation_sub' => 'つまり、あなたの利益は32ptになります。'],
    ])->add_page('ルール説明', [
        ['explanation' => 'この投資をメンバーを変えずに'. strval($_con->get(VAR_TURN_NO_PUNISH, 0)) .'ターン繰り返します。'],
        ['explanation_sub' => '投資できる最大額は毎ターン20ポイントです。'],
        ['explanation_sub' => '各ターンで得られた利益は、累積されてページ上部に表示されます。'],
        ['explanation_sub' => '各ターン毎に、他のユーザーの投資額が表示されます。'],
        ['explanation_blank' => ''],
        ['explanation' => 'あなたは、なるべく総利益が最大になるように投資してください。'],
    ])->add_page('待機', [
        ['explanation' => 'それでは実験開始までしばらくお待ち下さい。'],
    ]);


$punish_explanation = new ExplanationUI($_con, 'punish');
$punish_explanation->add_page('ルール説明(罰則あり)', [
    ['explanation' => '同様の実験に罰則制度を設けてもう一度行います。'],
    ['explanation' => 'グループの各メンバーはそれぞれ20ポイントずつ持っており、その一部または全部を投資するところまでは同じです。'],
    ['explanation' => '次のような罰則制度を新たに設けます。'],
    ['explanation_sub' => '各ターン毎に、他のユーザの投資額が表示された後、罰則を入力する画面が表示されます。'],
    ['explanation_sub' => 'あなたは、自分のポイントから罰則コストとして1ポイント使うと、相手から3ポイント減じることができます。'],
    ['explanation_sub' => '罰則は何人にでも与えることができますが、罰則に使えるポイントは最大10ポイントまでです。'],
    ['explanation_sub' => '罰則によって自分、もしくは相手の利益がマイナスになることがあります。'],
    ['explanation' => '最終的に、あなたの利益は次のように計算されます。'],
    ['explanation_sub' => 'あなたの利益=20pt-あなたの投資額+(0.4×グループ全員の投資額合計)-あなたが使った罰則ポイント-相手から受けた罰則ポイント'],
    ['explanation' => 'この罰則ありの投資をメンバーを変えずに'. strval($_con->get(VAR_TURN_PUNISH, 0)) .'ターン繰り返します。'],
]);
$pages[PAGE_PUNISH_EXPLANATION]->add($punish_explanation);

$pages[PAGE_PUNISH_EXPLANATION]->add(new ButtonUI($_con,
    function($con) {
        return '確認';
    },
    function($con) { 
        $con->set_personal(VAR_READY, true); 
        if ( isReady($con, calcNumReadyUser($con)) ) {
            setValueToAllUsers($con, VAR_READY, false);
            redirectAllUsers($con, PAGE_EXPERIMENT);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_EXPERIMENT]->add(new TemplateUI(<<<TMPL
{if punish==0}
<h1 style="text-align: center;">投資ポイント入力(罰則なし実験)</h1>
{else}
<h1 style="text-align: center;">投資ポイント入力(罰則あり実験)</h1>
{/if}
<hr/><br/>
現在のターン数は{turn}回目です。<br/>
この実験は全部で{total_turn}回行います。<br/>
{if left_turn==0}このターンが最後の回です。<br/>
{else}残りのターン数はこのターンを含めて{left_turn}回です。<br/>{/if}
<br/><hr/><br/>
あなたは{cur_pt}ポイント持っています。<br/>
{cur_pt}ポイントのうち、一部または全部をグループで実施するプロジェクトのために投資することができます。<br/>
{cur_pt}ポイントをそのまま持っておく分と、プロジェクトに投資する分に分けてください。<br/>
<br/>
入力すると、グループの他のメンバーの投資額を計算したあとで、あなたの利益は次のように計算されます。<br/><br/>
{if punish==0}
<center><i><b>あなたの利益＝{cur_pt}−あなたの投資ポイント＋(0.4×グループ全員の合計投資ポイント)</b></i></center>
{else}
<center><i><b>あなたの利益＝{cur_pt}−あなたの投資ポイント＋(0.4×グループ全員の合計投資ポイント) - 罰則に用いたポイント - (3×他のメンバーから受けた合計罰則ポイント)</b></i></center>
{/if}
<br/><hr/><br/>
プロジェクトに何ポイント投資しますか？<br/>
TMPL
,   function()use($_con) {
        $cur_group = intval($_con->get_personal(VAR_GROUP, 0));
        $is_punish = isPunishPhase($_con);
        $turn_id   = $is_punish ? VAR_TURN_PUNISH : VAR_TURN_NO_PUNISH;
        return [
            'punish'        => intval($is_punish),
            'turn'          => getValueByString($_con->get(VAR_TURN, 0), $cur_group),
            'total_turn'    => getValueByString($_con->get($turn_id, 0), $cur_group),
            'left_turn'     => $_con->get($turn_id, 0) - $_con->get(VAR_TURN, 0) + 1,
            'id'            => $_con->get_personal(VAR_CUR_ID, 0),
            'cur_pt'        => $_con->get_personal(VAR_CUR_PT, 0),
        ];
    }
));


$pages[PAGE_EXPERIMENT]->add(new SendingUI('投資する', 
    function($value)use($_con) {
        $invest_pt  = intval($value);
        $cur_pt     = $_con->get_personal(VAR_CUR_PT, 0);
        if ( !isValidValue($invest_pt, 0, $cur_pt) ) {
            return;
        }

        $_con->set_personal(VAR_INVEST_PT, $invest_pt);
        appendInvestmentData($_con, $invest_pt);

        $_con->set_personal(VAR_READY, true); 
        if ( isReady($_con, calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_MIDDLE_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));
$pages[PAGE_EXPERIMENT]->add(new TemplateUI(<<<TMPL
<br/><br/><hr/>
<div style="text-align: right;">なお、現在の累計ポイント数は{total_profit}ポイントです。</div>
TMPL
,   function()use($_con) {
        return [
            'total_profit'  => $_con->get_personal(VAR_TOTAL_PROFIT, 0)
        ];
    }
));


$pages[PAGE_PUNISHMENT]->add(new TemplateUI(<<<TMPL
<h1 style="text-align: center;">第{turn}回の罰則ポイント入力</h1>
<hr/><br/>
あなたは他のメンバーに対する罰則に合計{punish_pt}ポイントまで使えます。<br/>
あなたが相手に1ポイントの罰則を使うと、あなたの累計ポイントが1ポイント減らされ、相手は累計ポイントが3ポイント減らされます。<br/>
それぞれのメンバーにいくらの罰則を与えますか。<br/><br/>
TMPL
,   function()use($_con) {
        $cur_group = intval($_con->get_personal(VAR_GROUP, 0));
        return [
            'turn'          => getValueByString($_con->get(VAR_TURN, 0), $cur_group),
            'id'            => $_con->get_personal(VAR_CUR_ID, 0), 
            'punish_pt'     => $_con->get_personal(VAR_CUR_PUNISH_PT, 0), 
        ]; 
    }
));

$pages[PAGE_PUNISHMENT]->add(new MultiSendingUI('罰則を与える',
    call_user_func(
        function($con) {
            $cur_group  = intval($con->get_personal(VAR_GROUP, 0));
            $list       = [];
            foreach ( $con->participants as $participant ) {
                $id     = $participant[VAR_ID];
                $group  = $con->get_personal(VAR_GROUP, 0, strval($id));
                if ( $group != $cur_group) {
                    continue;
                }

                if ( isCurrentUser($con, $id) ) {
                    $cur_id = $con->get_personal(VAR_CUR_ID, 0);
                    continue;
                }

                $invest_pt      = $con->get_personal(VAR_INVEST_PT, 0, strval($id));
                $description    = 'IDが' . $id . 'のメンバー(' . $invest_pt . 'ポイント投資)に対する罰則ポイント';
                $list[] = [
                    'id'            => $id,
                    'description'   => $description,
                    'dvalue'        => '',
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
        if ( isReady($_con, calcNumReadyUser($_con)) ) {
            setValueToAllUsers($_con, VAR_READY, false);
            redirectAllUsers($_con, PAGE_PUNISHMENT_RESULT);
        } else {
            redirectCurrentUser($_con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_WAIT_ACTION]->add(new TemplateUI(<<<TMPL
<h1 style="text-align: center;">入力待ち</h1>
<hr/><br/>
<center>あと{num_not_ready_user}名の入力を待っています。</center>
TMPL
,   function()use($_con) { 
        $num_not_ready_user = $_con->get(VAR_NUM_PLAYER, 0) - calcNumReadyUser($_con);

        return [
            'num_not_ready_user'    => $num_not_ready_user
        ];
    }
));


$pages[PAGE_PUNISHMENT_RESULT]->add(new TemplateUI(<<<TMPL
<h1 style="text-align: center;">第{turn}回の罰則結果</h1>
<hr/><br/>
<table  class="pure-table">
<thead>
<tr><th>あなたが使用した罰則ポイント</th><th>あなたが受けた罰則結果*</th></tr>
</thead>
<tbody align="right">
<tr class="pure-table-odd">
<td>{punish}ポイント</td><td>{received_punish}ポイント</td>
</tr>
</tbody>
</table>
* あなたがそれぞれのメンバーから受けた罰則ポイントを3倍したポイントのことで、累計ポイントから減算されるポイントです。<br/>
<br/>
TMPL
,   function()use($_con) {
        $cur_group      = intval($_con->get_personal(VAR_GROUP, 0));
        $turn_string    = $_con->get(VAR_TURN);
        $turn           = intval(getValueByString($turn_string, $cur_group));
        $punish_pt      = $_con->get_personal(VAR_PUNISH_PT, 0);
        $pt             = $_con->get_personal(VAR_RECEIVED_PUNISH_PT, 0);

        return [
            'turn'              => $turn,
            'punish'            => $punish_pt,
            'received_punish'   => $pt,
        ];
    }
));

$pages[PAGE_PUNISHMENT_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return '確認';
    },
    function($con) {
        reduceTotalProfit($con);

        $con->set_personal(VAR_READY, true);
        if ( isReady($con, calcNumReadyUser($con)) ) {
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
<h1 style="text-align: center;">第{turn}回の結果</h1>
<hr/><br/>
<table  class="pure-table">
<thead>
<tr><th>メンバーのID</th><th>投資ポイント</th><th>備考</th></tr>
</thead>
<tbody align="right">
{each invest_list}
<tr{if self} class="pure-table-odd"{/if}>
<td>{id}</td><td>{pt}ポイント</td><td>{if self}あなた{/if}</td>
</tr>
{/each}
</tbody>
</table>
<br/>
TMPL
,   function()use($_con) {
        $cur_group      = intval($_con->get_personal(VAR_GROUP, 0));
        $turn_string    = $_con->get(VAR_TURN);
        $turn           = intval(getValueByString($turn_string, $cur_group));
        $invest_list    = [];
        $self           = [];
        foreach ( $_con->participants as $participant ) {
            $id             = $participant[VAR_ID];
            $group          = $_con->get_personal(VAR_GROUP, 0, $id);
            if ( $cur_group != $group ) {
                continue;
            }

            $pt             = $_con->get_personal(VAR_INVEST_PT, 0, strval($id));
            if($participant[VAR_ID]== isCurrentUser($_con, $id)){
                $invest_list[]  = [VAR_ID => $id, 'pt' => $pt, 'self' => true];
            }else{
                $invest_list[]  = [VAR_ID => $id, 'pt' => $pt, 'self' => false];
            }
        } 
        return [
            'turn'          => $turn,
            'id'            => $_con->get_personal(VAR_CUR_ID, 0),
            'invest_list'   => $invest_list,
            'self'          => $self
        ];
    }
));

$pages[PAGE_MIDDLE_RESULT]->add(new ButtonUI($_con,
    function($con) {
        return '確認';
    },
    function($con) {
        setTotalProfit($con);

        $con->set_personal(VAR_READY, true);
        if ( isReady($con, calcNumReadyUser($con)) ) {
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
<h1 style="text-align: center;">最終結果</h1>
<hr/><br/>
<table  class="pure-table">
<thead>
<tr><th>メンバーのID</th><th>累計ポイント</th><th>総投資ポイント</th><th>投資率</th><th>総罰則ポイント</th><th>備考</th></tr>
</thead>
<tbody align="right">
{each total_profit_list}
<tr{if self} class="pure-table-odd"{/if}>
<td>{id}</td><td>{pt}ポイント</td><td>xxxポイント</td><td>xx.x%</td><td>xxxポイント</td><td>{if self}あなた{/if}</td>
</tr>
{/each}
</tbody>
</table>
<br/>
TMPL
,   function()use($_con) {
        $cur_group          = intval($_con->get_personal(VAR_GROUP, 0));
        $total_profit_list  = [];
        foreach ( $_con->participants as $participant ) {
            $id                     = $participant[VAR_ID];
            $group                  = $_con->get_personal(VAR_GROUP, 0, $id);
            if ( $cur_group != $group ) {
                continue;
            }

            $pt                     = $_con->get_personal(VAR_TOTAL_PROFIT, 0, strval($id));
            if($participant[VAR_ID]== isCurrentUser($_con, $id)){
                $total_profit_list[]  = [VAR_ID => $id, 'pt' => $pt, 'self' => true];
            }else{
                $total_profit_list[]  = [VAR_ID => $id, 'pt' => $pt, 'self' => false];
            }
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
        $cur_group      = $con->get_personal(VAR_GROUP, 0);
        $turn_string    = $con->get(VAR_TURN);
        $turn           = intval(getValueByString($turn_string, $cur_group));
        $turn_punish    = intval($con->get(VAR_TURN_PUNISH));
        if ( !isPunishPhase($con) && $turn_punish > 0 ) {
            return '次の実験へ';
        }

        return '実験を終了する';
    },
    function($con) { 
        $con->set_personal(VAR_READY, true);
        if ( isReady($con, calcNumReadyUser($con)) ) {
            $cur_group      = $con->get_personal(VAR_GROUP, 0);
            $turn_string    = $con->get(VAR_TURN);
            $turn           = intval(getValueByString($turn_string, $cur_group));
            if ( isFinishAllPhase($con, $turn) ) {
                redirectAllUsers($con, PAGE_GRAPH);
            } else {
                $con->set(VAR_TURN, setValueToString($turn_string, $cur_group, 1));
                if ( changePhase($con) == 0 ) {
                    redirectAllUsers($con, PAGE_GRAPH);
                } else {
                    initAllUsersData($con);
                    setValueToAllUsers($con, VAR_TOTAL_PROFIT, 0);
                    redirectAllUsers($con, PAGE_PUNISH_EXPLANATION);
                }
            }
            setValueToAllUsers($con, VAR_READY, false);
        } else {
            redirectCurrentUser($con, PAGE_WAIT_ACTION);
        }
    }
));


$pages[PAGE_GRAPH]->add(new StaticUI('Graph<br/>未実装です┌(^o^ ┐)┐'));

$pages[PAGE_GRAPH]->add(new ScatterGraph(
    call_user_func(
        function()use($_con) {
            $mean_invest_list = calcMeanInvestment($_con);

            $data = [
                'no_punish' => [
                    'values' => [],
                    'color' => 'green',
                ],
                'punish' => [
                    'values' => [],
                    'color' => 'red',
                ],
            ];

            $counter = 0;
            foreach ( $mean_invest_list as $mean_invest ) {
                ++$counter;
                if ( !isPunishmentData($_con, $counter) ) {
                   $data['no_punish']['values'][] = ['x' => $counter, 'y' => $mean_invest]; 
                } else {
                   $data['punish']['values'][] = ['x' => $counter, 'y' => $mean_invest];
                }
            }

            return $data;
        }
    ),
    ['label' => ['x' => 'ターン', 'y' => '平均投資額']]
));



// add all pages
$_con->add_component($_page = new PageContainer(
    function()use($_con) { 
        return $_con->get_personal(VAR_PAGE, PAGE_WAIT); 
    }
));

foreach ($pages as $key => $value) {
    $_page->add_page($key, $value);
}

