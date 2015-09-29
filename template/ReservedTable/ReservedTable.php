<?php

/////////////////////////////////////
//
//  header()が動かねぇ！if(isset($_GET["date"])){
//		代わりにinclude使用
//
///////////////////////////////////////
require "function.php";

// 現在の年月を取得
$year = date('Y');
$month = date('n');

if(isset($_GET["date"])){
	$array = array('year' => $year , 'month' => $month , 'day' => $_GET["date"]);
	$json=json_encode($array);//http://php.net/manual/ja/function.json-encode.php 配列をjsonに変換
	postFromHTTP("Select.php", $json);
	include("Select.php");
}
// 月末日を取得
$last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));
 
$calendar = array();
$j = 0;
 
// 月末日までループ
for ($i = 1; $i < $last_day + 1; $i++) {
 
    // 曜日を取得
    $week = date('w', mktime(0, 0, 0, $month, $i, $year));
 
    // 1日の場合
    if ($i == 1) {
 
        // 1日目の曜日までをループ
        for ($s = 1; $s <= $week; $s++) {
 
            // 前半に空文字をセット
            $calendar[$j]['day'] = '';
            $j++;
 
        }
 
    }
 
    // 配列に日付をセット
    $calendar[$j]['day'] = $i;
    $j++;
 
    // 月末日の場合
    if ($i == $last_day) {
 
        // 月末日から残りをループ
        for ($e = 1; $e <= 6 - $week; $e++) {
 
            // 後半に空文字をセット
            $calendar[$j]['day'] = '';
            $j++;
 
        }
 
    }
 
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
</head>
<body>
<div class="pure-u-24-24">
<?php echo $year; ?>年<?php echo $month; ?>月のカレンダー
<br>
<br>
<table>
    <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
    </tr>
 
    <tr>
    <?php $cnt = 0; ?>
    <?php foreach ($calendar as $key => $value): ?>
 
        <td><a href="?date=<?php echo $value['day']; ?>">

        <?php $cnt++; ?>
        <?php echo $value['day']; ?>
		</a>
		</td>
 
		<?php if ($cnt == 7): ?>
		</tr>
		<tr>
		<?php $cnt = 0; ?>
		<?php endif; ?>
 
    <?php endforeach; ?>
    </tr>
</table>

</div>


</body>
</html>
<style type="text/css">
table {
    width: 100%;
}
table th {
    background: #EEEEEE;
}
table th,
table td {
    border: 1px solid #CCCCCC;
    text-align: center;
    padding: 5px;
}
</style>