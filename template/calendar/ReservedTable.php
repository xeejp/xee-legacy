<?php

/////////////////////////////////////
//
// このコードの読み方
//    @1 -> @2 -> @3  の順番でコメントを検索しよう！！！
// 
//
//
///////////////////////////////////////

   $url = "json.json";
   $json_c = file_get_contents($url);//ファイルの内容を全て文字列に読み込む
   $array = json_decode($json_c,true); //jsonを配列に変換http://qiita.com/doima_/items/a8069fddf12ce10ede76 
   $array_length = count($array);
   

// 現在の年月を取得^
$year = date('Y');
$month = date('n');
$day=0;



$Select="";///////////////////////////////////////////////////////////////@2日付をクリックした後の処理。フォーム画面を出力する。
if(isset($_GET["date"])){
	$array_send_date = array('year' => $year , 'month' => $month , 'day' => $_GET["date"]);
	$Select="<form method=\"post\" action=\"\">開始時間<select name=\"start_time\"><option value=\"0\">0時</option><option value=\"1\">1時</option><option value=\"2\">2時</option><option value=\"3\">3時</option><option value=\"4\">4時</option><option value=\"5\">5時</option><option value=\"6\">6時</option><option value=\"7\">7時</option><option value=\"8\">8時</option><option value=\"9\">9時</option><option value=\"10\">10時</option><option value=\"11\">11時</option><option value=\"12\">12時</option><option value=\"13\">13時</option><option value=\"14\">14時</option><option value=\"15\">15時</option><option value=\"16\">16時</option><option value=\"17\">17時</option><option value=\"18\">18時</option><option value=\"19\">19時</option><option value=\"20\">20時</option><option value=\"21\">21時</option><option value=\"22\">22時</option><option value=\"23\">23時</option><option value=\"24\">24時</option>	</select>終了時間<select name=\"end_time\"><option value=\"0\">0時</option><option value=\"1\">1時</option><option value=\"2\">2時</option><option value=\"3\">3時</option><option value=\"4\">4時</option><option value=\"5\">5時</option><option value=\"6\">6時</option><option value=\"7\">7時</option><option value=\"8\">8時</option><option value=\"9\">9時</option><option value=\"10\">10時</option><option value=\"11\">11時</option><option value=\"12\">12時</option><option value=\"13\">13時</option><option value=\"14\">14時</option><option value=\"15\">15時</option><option value=\"16\">16時</option><option value=\"17\">17時</option><option value=\"18\">18時</option><option value=\"19\">19時</option><option value=\"20\">20時</option><option value=\"21\">21時</option><option value=\"22\">22時</option><option value=\"23\">23時</option><option value=\"24\">24時</option>	</select><label>参加人数<input type=\"number\" name=\"number\"　min=\"1\" max=\"1000\"></label><label>名前<input type=\"text\" name=\"name\"></label><input type = \"submit\" name=\"exe\" value = \"送信\"></form>";
}
if(isset($_POST['exe'])){//////////////////////////////////////////////////////////////////////////@3フォームで送られてきた内容を配列に代入して出力する
	
	$array_send_date['start_time']=$_POST['start_time'];
	$array_send_date['end_time']=$_POST['end_time'];
	$array_send_date['number']=$_POST['number'];
	$array_send_date['name']=$_POST['name'];

	print "<pre>";
	print_r($array_send_date);
	print "</pre>";
	
}
//////////////////////////////////////////////////////////////////////////////////////////////////////カレンダー作成
// 月末日を取得
$last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));
 
$calendar = array();
$j = 0;
$first_space=0;
$week=0;
$first=array($first_space);
$json_num = json_encode($first);
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
		$first_space=$j;
 
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

if(!isset($_GET["date"])){///////////////////////////////////////////////////////////////////////@1カレンダーを色付けする準備。カレンダーの情報をjsに送る役割がある。
$total_number=array_fill(1,$last_day,0);

for($i=0;$i<$array_length;$i++){
	$total_number[$array[$i]["day"]] += $array[$i]["number"];
}
    echo "<br>"."total_number";
   	print "<pre>";
	print_r($total_number);
	print "</pre>";
   $json_number = json_encode($total_number);
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
予約済み人数：０～３００人->青、３００～６００人->緑、６００～９００人->赤、９００～->予約不可
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
 
        <td>
		
		<a href="?date=<?php echo $value['day']; ?>" class="bc">

        <?php $cnt++; ?>
        <?php echo $value['day']; ?>
		</a>
		</div>
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

<?php echo $Select;?>


<script type="text/javascript">

var total_member=JSON.parse('<?php echo $json_number;?>');
var first_space=JSON.parse('<?php echo $json_num;?>');
window.onload=init;

function init(){
	var element=document.getElementsByClassName("bc") ;
	
	for(var i=0;i<35;i++){
		
		if((i+1)>first_space){

			if(300>total_member[i-first_space-1]){
				element[i].style.backgroundColor='#A9D0F5';//青
			}
			else{
				if(600>total_member[i-first_space-1]){
					element[i].style.backgroundColor='#D0F5A9';//緑
				}
				else{
					if(900>total_member[i-first_space-1]){
						element[i].style.backgroundColor='#F5A9A9';//赤
					}
					else{
						element[i].removeAttribute("href");//人が多過ぎたらクリック不可能に
					}
				}
			}
		}
	}
}
</script>	
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