<?php
if(isset($_POST['number'])){
   $url = "json.json";
   $json_c = file_get_contents($url);//ファイルの内容を全て文字列に読み込む
   $array = json_decode($json_c,true); //jsonを配列に変換
   shuffle($array);
   $array_length = count($array);
   $member=array();
   $dt=0;
   $member_number = floor($array_length / $_POST['number']);
   
	for($q=0;$q<$member_number;$q++){
	   for($i=0;$i<$_POST['number'];$i++){
		$member[$q][$i]=$array[$dt];
		$dt++;

		}
	}
	print "<pre>";
	print_r($member);
	print "</pre>";

}  
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<form method="post" action"">
	１グループの人数<input type="number" name="number"　min="1" max="1000">
	<input type = "submit" name="exe" value = "送信">
</form>
</body>
</html>