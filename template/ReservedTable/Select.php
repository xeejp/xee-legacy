<?php
//require "function.php";

$array="";
if(isset($_POST)){
	$array=json_decode($json,true);
	
	if(isset($_POST['start_time'])){
		$array['start_time'] = $_POST['start_time'];
	}
	
	if(isset($_POST['end_time'])){
		$array['end_time'] = $_POST['end_time'];
	}
	if(isset($_POST['number'])){
		$array['number'] = $_POST['number'];
	}
	if(isset($_POST['name'])){
		$array['name'] = $_POST['name'];
		
		$json=json_encode($array);
		postFromHTTP("Finish.php", $json);
		include("Finish.php");
	}
	
}


?>


<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
</head>
<body>

<form method="post" action="">
	<label>開始時間<select name="start_time"></label>
	<?php
		for($dt=0;$dt<=24;$dt++){
				echo "<option value=\"".$dt."\">".$dt."時</option>";
		}
	?>
	</select>
	<label>終了時間<select name="end_time"></label>
	<?php
		for($dt=0;$dt<=24;$dt++){
				echo "<option value=\"".$dt."\">".$dt."時</option>";
		}
	?>
	</select>
	<label>参加人数<input type="number" name="number"　min="1" max="100"></label>
	<label>名前<input type="text" name="name"></label>
	<input type = "submit" name="exe" value = "送信">
</form>

</body>
</html>