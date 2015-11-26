<?php

require 'common.php';
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
<th>グループ番号</th>
<th>ターン数</th>
<th>累計pt</th>
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
<td align="center">{if read_exp}読了{else}未読{/if}</td>
<td>{cur_group}</td>
<td>{cur_turn}</td>
<td>{total_profit}</td>
<td>{if finished}終了{else}実験中{/if}</td>
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
$container->add(new StaticUI('<h2>実験番号: <font style="color:red;">'. $_con->experiment[EXP_NO] .'</font></h2></div><hr/>'));


// settings
$container->add(new MultiSendingUI('設定',
    call_user_func(
        function($_con) {
            $list = [
                ['id' => VAR_NUM_PLAYER,        'description' => '1グループあたりの人数',       'dvalue' => $_con->get(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER)],
                ['id' => VAR_TURN_NO_PUNISH,    'description' => '罰なし実験の繰り返し回数',    'dvalue' => $_con->get(VAR_TURN_NO_PUNISH, DEFAULT_TURN)],
                ['id' => VAR_TURN_PUNISH,       'description' => '罰あり実験の繰り返し回数',    'dvalue' => $_con->get(VAR_TURN_PUNISH, DEFAULT_TURN)]
            ];

            return $list;
        }
        ,$_con
    ),
    function($value)use($_con) {
        $num_player     = intval($value[VAR_NUM_PLAYER]);
        $turn_no_punish = intval($value[VAR_TURN_NO_PUNISH]);
        $turn_punish    = intval($value[VAR_TURN_PUNISH]);
        if ( !(isValidValue($turn_no_punish, 1, MAX_TURN) || isValidValue($turn_punish, 1, MAX_TURN)) ) {
            return;
        }

        $_con->set(VAR_NUM_PLAYER, $num_player);
        $_con->set(VAR_TURN_NO_PUNISH, $turn_no_punish);
        $_con->set(VAR_TURN_PUNISH, $turn_punish); 
    }
)); 

$container->add(new TemplateUI(<<<TMPL
<br/>
現在の設定値<br/>
1グループあたりの人数：{if num_player==0}未設定{else}{num_player}{/if}<br/>
罰なし: {if turn_no_punish==0}未設定{else}{turn_no_punish}回{/if}<br/>
罰あり: {if turn_punish==0}未設定{else}{turn_punish}回{/if}<br/>
<br/>
TMPL
,   function()use($_con) {
        if ( $_con->get(VAR_NUM_PLAYER, 0) == 0 ) {
            $_con->set(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);
        }
        if ( $_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ( $_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
        $list = [
            'num_player'        => strval($_con->get(VAR_NUM_PLAYER, 0)),
            'turn_no_punish'    => strval($_con->get(VAR_TURN_NO_PUNISH, 0)),
            'turn_punish'       => strval($_con->get(VAR_TURN_PUNISH, 0))
        ];

        return $list;
    } 
));

// participants
$container->add(new TemplateUI($templates['participants_list'], function () use ($_con) {
    $participants = [];
    foreach ($_con->participants as $participant)
        $participants[] = [
            'id' => $participant['id'],
            'name' => $participant['name'],
            'active' => $_con->get_personal('active', true, $participant['id']),
	    'page' => $_con->get_personal('page', 'none', $participant['id']),
            'read_exp' => $_con->get_personal('ExpUI::no', 0, $participant['id']) == 5,
            'cur_turn'        => getValueByString($_con->get(VAR_TURN, 0), $_con->get_personal(VAR_GROUP, -1, $participant['id'])),
            'total_profit'  => $_con->get_personal(VAR_TOTAL_PROFIT, 0, $participant['id']),
            'cur_group'      => $_con->get_personal(VAR_GROUP, -1, $participant['id']),
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
$container->add($modulator = new PageContainer(
    function()use($_con) {
        return $_con->get(VAR_PAGE, PAGE_WAIT);
    }
));

$modulator->add_page(PAGE_WAIT, new MatchingButton($_con,
    function ($_con) {
/*        $num = 0;
        foreach ($_con->participants as $participant) {
            if ($_con->get_personal('active', true, $participant['id'])) $num++;
        }
        return $num > 0;
*/
	return true;
    },
    function($_con) {
	$num = 0;

	$result = [];
	$groups = [];
	$num_player = $_con->get(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);
	$cur_max_group = 0;
//すでにマッチングされている人たちの中で、グループ人数が$num_playerに届かないチームは無効にする。
        foreach ($_con->participants as $participant) {
		if($_con->get_personal('active', true, $participant['id'])){
			$groups[ intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) ][] = $participant['id'];
		}else{
			$_con->set_personal('active', false, $participant['id']);
			$_con->set_personal(VAR_GROUP, -1, $participant['id']);
		}

		if($cur_max_group < intval($_con->get_personal(VAR_GROUP, -1, $participant['id']))){
			$cur_max_group = intval($_con->get_personal(VAR_GROUP, -1, $participant['id']));
		}
	}

	unset($groups[-1]);
	foreach($groups as $group){
		if(count($group) < $num_player){
			foreach($group as $p_id){
				$_con->set_personal('active', false, $p_id);
				$_con->set_personal(VAR_GROUP, -1, $p_id);
			}
		}
	}

//マッチングされていない人たちの中で、active=trueの人たちをマッチングする。
	$id_list = [];
	//参加ユーザのグループ設定
        foreach ($_con->participants as $participant) {
		if ($_con->get_personal('active', true, $participant['id']) && (intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) < 0)) {
			$id_list[] = $participant['id'];
		}
	}
        shuffle($id_list);
	$max_num_player = floor(count($id_list) / $num_player) * $num_player;
	for($i = 0; $i < $max_num_player; $i++){
		// 参加者は初期値を設定する。
		$group_num = $cur_max_group + floor($i / $num_player);
		$_con->set_personal(VAR_GROUP, $group_num, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 20, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 10, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', true, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($id_list[$i]));
        }
	for($i = $max_num_player; $i < count($id_list); $i++){
		// 不参加者はすべての設定を削除する。
		$_con->set_personal(VAR_GROUP, -1, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', false, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($id_list[$i]));
	}

        foreach ($_con->participants as $participant) {
            $id = $participant[VAR_ID];
            $group = $_con->get_personal(VAR_GROUP, -1, strval($id));
            if ( $group == -1 ) {
                continue;
            }

            if ($_con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID]))) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($participant[VAR_ID]));
		$num++;
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
            }
        }

        $_con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, 1);
        $total_turn_array   = array_fill(0, $num, 1);
        $punish_phase_array = array_fill(0, $num, 0);
        
        $_con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $_con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $_con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));

        if ($_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ($_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
        $_con->set(VAR_PAGE, 'ready');
	return $result;
    }
));


$modulator->add_page('ready', $_ready = new NormalContainer());


$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "余っている人だけマッチング（うまくいっている人はそのまま）"; 
    },
    function($_con) {
	$num = 0;

	$result = [];
	$groups = [];
	$num_player = $_con->get(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);
	$cur_max_group = 0;
//すでにマッチングされている人たちの中で、グループ人数が$num_playerに届かないチームは無効にする。
        foreach ($_con->participants as $participant) {
		if($_con->get_personal('active', true, $participant['id'])){
			$groups[ intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) ][] = $participant['id'];
		}else{
			$_con->set_personal('active', false, $participant['id']);
			$_con->set_personal(VAR_GROUP, -1, $participant['id']);
		}

		if($cur_max_group < intval($_con->get_personal(VAR_GROUP, -1, $participant['id']))){
			$cur_max_group = intval($_con->get_personal(VAR_GROUP, -1, $participant['id']));
		}
	}

	unset($groups[-1]);
	foreach($groups as $group){
		if(count($group) < $num_player){
			foreach($group as $p_id){
				$_con->set_personal('active', false, $p_id);
				$_con->set_personal(VAR_GROUP, -1, $p_id);
			}
		}
	}

//マッチングされていない人たちの中で、active=trueの人たちをマッチングする。
	$id_list = [];
	//参加ユーザのグループ設定
        foreach ($_con->participants as $participant) {
		if ($_con->get_personal('active', true, $participant['id']) && (intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) < 0)) {
			$id_list[] = $participant['id'];
		}
	}
        shuffle($id_list);
	$max_num_player = floor(count($id_list) / $num_player) * $num_player;
	for($i = 0; $i < $max_num_player; $i++){
		// 参加者は初期値を設定する。
		$group_num = $cur_max_group + floor($i / $num_player) + 1;
		$_con->set_personal(VAR_GROUP, $group_num, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 20, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 10, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', true, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($id_list[$i]));

		$num++;
        }
	for($i = $max_num_player; $i < count($id_list); $i++){
		// 不参加者はすべての設定を削除する。
		$_con->set_personal(VAR_GROUP, -1, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', false, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($id_list[$i]));
	}

        foreach ($_con->participants as $participant) {
            $id = $participant[VAR_ID];
            $group = $_con->get_personal(VAR_GROUP, -1, strval($id));
            if ( $group == -1 ) {
                continue;
            }

            if ($_con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID]))) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($participant[VAR_ID]));
		$num++;
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
            }
        }

        $_con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, 1);
        $total_turn_array   = array_fill(0, $num, 1); 
        $punish_phase_array = array_fill(0, $num, 0);
        
        $_con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $_con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $_con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));

        if ($_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ($_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
        $_con->set(VAR_PAGE, 'ready');
	return $result;
    }
));
$_ready->add(new ButtonUI($_con,
    function($_con) {
        return "開始"; 
    },
    function($_con) {
/*
	$result = [];
	$groups = [];
	$num_player = $_con->get(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);

	//すでにマッチングされている人たちの中で、グループ人数が$num_playerに届かないチームは無効にする。
        foreach ($_con->participants as $participant) {
		if($_con->get_personal('active', true, $participant['id'])){
			$groups[ intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) ][] = $participant['id'];
		}else{
			$_con->set_personal('active', false, $participant['id']);
			$_con->set_personal(VAR_GROUP, -1, $participant['id']);
		}
	}

	unset($groups[-1]);
	foreach($groups as $group){
		if(count($group) < $num_player){
			foreach($group as $p_id){
				$_con->set_personal('active', false, $p_id);
				$_con->set_personal(VAR_GROUP, -1, $p_id);
			}
		}
	}
*/
        $_con->set(VAR_PAGE, PAGE_EXPERIMENT);
        foreach ($_con->participants as $participant) {
            $id = $participant[VAR_ID];
            $group = $_con->get_personal(VAR_GROUP, -1, strval($id));
            if ( $group == -1 ) {
                continue;
            }
            if ($_con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID]))) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPERIMENT, strval($participant[VAR_ID]));
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
            }
        }
    }
));


$modulator->add_page(PAGE_EXPERIMENT, $_proceeding = new NormalContainer());

$_proceeding->add(new ButtonUI($_con,
    function($_con) {
        return "余っている人だけマッチング（うまくいっている人はそのまま）"; 
    },
    function($_con) {
	$num = 0;

	$result = [];
	$groups = [];
	$num_player = $_con->get(VAR_NUM_PLAYER, DEFAULT_NUM_PLAYER);
	$cur_max_group = 0;
//すでにマッチングされている人たちの中で、グループ人数が$num_playerに届かないチームは無効にする。
        foreach ($_con->participants as $participant) {
		if($_con->get_personal('active', true, $participant['id'])){
			$groups[ intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) ][] = $participant['id'];
		}else{
			$_con->set_personal('active', false, $participant['id']);
			$_con->set_personal(VAR_GROUP, -1, $participant['id']);
			$_con->set_personal(VAR_PAGE, PAGE_WAIT, $participant['id']);
		}

		if($cur_max_group < intval($_con->get_personal(VAR_GROUP, -1, $participant['id']))){
			$cur_max_group = intval($_con->get_personal(VAR_GROUP, -1, $participant['id']));
		}
	}

	unset($groups[-1]);
	foreach($groups as $group){
		if(count($group) < $num_player){
			foreach($group as $p_id){
				$_con->set_personal('active', false, $p_id);
				$_con->set_personal(VAR_GROUP, -1, $p_id);
				$_con->set_personal(VAR_PAGE, PAGE_WAIT, $p_id);
			}
		}
	}

//マッチングされていない人たちの中で、active=trueの人たちをマッチングする。
	$id_list = [];
	//参加ユーザのグループ設定
        foreach ($_con->participants as $participant) {
		if ($_con->get_personal('active', true, $participant['id']) && (intval($_con->get_personal(VAR_GROUP, -1, $participant['id'])) < 0)) {
			$id_list[] = $participant['id'];
		}
	}
        shuffle($id_list);
	$max_num_player = floor(count($id_list) / $num_player) * $num_player;
	for($i = 0; $i < $max_num_player; $i++){
		// 参加者は初期値を設定する。
		$group_num = $cur_max_group + floor($i / $num_player) + 1;
		$_con->set_personal(VAR_GROUP, $group_num, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 20, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 10, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', true, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($id_list[$i]));

		$num++;
        }
	for($i = $max_num_player; $i < count($id_list); $i++){
		// 不参加者はすべての設定を削除する。
		$_con->set_personal(VAR_GROUP, -1, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_ID, $id_list[$i], strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($id_list[$i]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($id_list[$i])); 
		$_con->set_personal(VAR_READY, false, strval($id_list[$i]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($id_list[$i]));
		$_con->set_personal(VAR_FINISH, false, strval($id_list[$i]));
		$_con->set_personal('active', false, strval($id_list[$i]));
		$_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($id_list[$i]));
	}

        foreach ($_con->participants as $participant) {
            $id = $participant[VAR_ID];
            $group = $_con->get_personal(VAR_GROUP, -1, strval($id));
            if ( $group == -1 ) {
                continue;
            }

            if ($_con->get_personal(VAR_ACTIVE, false, strval($participant[VAR_ID]))) {
                $_con->set_personal(VAR_PAGE, PAGE_EXPLANATION, strval($participant[VAR_ID]));
		$num++;
            } else {
                $_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
            }
        }

        $_con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, 1);
        $total_turn_array   = array_fill(0, $num, 1); 
        $punish_phase_array = array_fill(0, $num, 0);
        
        $_con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $_con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $_con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));

        if ($_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ($_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
        $_con->set(VAR_STATUS, PAGE_EXPERIMENT);
        $_con->set(VAR_PAGE, PAGE_EXPERIMENT);
	return $result;
    }
));


$_proceeding->add(new ButtonUI($_con,
    function($_con) {
        return '罰なし強制終了→罰ありへ'; 
    },
    function($_con) {
        $_con->set(VAR_STATUS, PAGE_EXPERIMENT);
        $_con->set(VAR_PAGE, PAGE_EXPERIMENT);
	$num = 0;
        foreach ($_con->participants as $participant) {
		$id = $participant[VAR_ID];
		$group = $_con->get_personal(VAR_GROUP, -1, strval($id));
		if ( $group == -1 ) {
			continue;
		}

		if($_con->get_personal('active', true, strval($participant[VAR_ID]))){
			$_con->set_personal(VAR_READY, true, strval($participant[VAR_ID]));
			$_con->set_personal(VAR_PAGE, PAGE_EXPERIMENT, strval($participant[VAR_ID]));
			$num++;
		}else{
			$_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
		}

        }

        $_con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, 1);
        $total_turn_array   = array_fill(0, $num, strval($_con->get(VAR_TURN_NO_PUNISH, 0)) + 1);
        $punish_phase_array = array_fill(0, $num, 1);
        
        $_con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $_con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $_con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));

        if ($_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ($_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }


    }
));

$_proceeding->add(new ButtonUI($_con,
    function($_con) {
        return '全体強制終了→結果表示へ'; 
    },
    function($_con) {
       $_con->set(VAR_STATUS, PAGE_EXPERIMENT);
        $_con->set(VAR_PAGE, PAGE_EXPERIMENT);
	$num = 0;
        foreach ($_con->participants as $participant) {
		$id = $participant[VAR_ID];
		$group = $_con->get_personal(VAR_GROUP, -1, strval($id));
		if ( $group == -1 ) {
			continue;
		}

		if($_con->get_personal('active', true, strval($participant[VAR_ID]))){
			$_con->set_personal(VAR_READY, true, strval($participant[VAR_ID]));
			$_con->set_personal(VAR_FINISH, true, strval($participant[VAR_ID]));
			$_con->set_personal('active', true, strval($participant[VAR_ID]));
			$_con->set_personal(VAR_PAGE, 'finalResult', strval($participant[VAR_ID]));
			$num++;
		}else{
			$_con->set_personal(VAR_PAGE, PAGE_REJECT, strval($participant[VAR_ID]));
		}

        }

        $_con->set(VAR_TOTAL_PLAYER, $num);
        $turn_array         = array_fill(0, $num, strval($_con->get(VAR_TURN_PUNISH, 0)) + 1);
        $total_turn_array   = array_fill(0, $num, strval($_con->get(VAR_TURN_NO_PUNISH, 0)) + strval($_con->get(VAR_TURN_PUNISH, 0)) + 1);
        $punish_phase_array = array_fill(0, $num, 1);
        
        $_con->set(VAR_TURN, implode(PUNCTUATION, $turn_array));
        $_con->set(VAR_TOTAL_TURN, implode(PUNCTUATION, $total_turn_array));
        $_con->set(VAR_PUNISH_PHASE, implode(PUNCTUATION, $punish_phase_array));

        if ($_con->get(VAR_TURN_NO_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_NO_PUNISH, DEFAULT_TURN);
        }
        if ($_con->get(VAR_TURN_PUNISH, 0) == 0 ) {
            $_con->set(VAR_TURN_PUNISH, DEFAULT_TURN);
        }
    }
));

$_proceeding->add(new ButtonUI($_con,
    function($_con) {
        return 'リセット'; 
    },
    function($_con) {
        $_con->set(VAR_STATUS, PAGE_WAIT);
        $_con->set(VAR_PAGE, PAGE_WAIT);
        foreach ($_con->participants as $participant) {
            $_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_GROUP, -1, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_CUR_ID, strval($participant[VAR_ID]), strval($participant[VAR_ID]));
		$_con->set_personal(VAR_CUR_PT, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_CUR_PUNISH_PT, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_TOTAL_PROFIT, 0.0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_INVEST_PT, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_PUNISH_PT, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_RECEIVED_PUNISH_PT, 0, strval($participant[VAR_ID])); 
		$_con->set_personal(VAR_READY, false, strval($participant[VAR_ID]));
		$_con->set_personal(ARRAY_INVEST_PT, '', strval($participant[VAR_ID]));
		$_con->set_personal(VAR_TOTAL_INVEST, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_TOTAL_PUNISH, 0, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_FINISH, false, strval($participant[VAR_ID]));
		$_con->set_personal('active', true, strval($participant[VAR_ID]));
		$_con->set_personal(VAR_PAGE, PAGE_WAIT, strval($participant[VAR_ID]));
        }
    }
));


$_con->add_component($container);
