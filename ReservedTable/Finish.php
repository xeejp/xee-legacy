<?php
if(isset($_POST)){
	$array=json_decode($json,true);
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<?php 
	print "<pre>";
	print_r($array);
	print "</pre>";
?>

</body>
</html>