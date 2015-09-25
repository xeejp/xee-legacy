<?php
   $url = "json.json";
   $json_c = "";
   $json_c = file_get_contents($url);//ファイルの内容を全て文字列に読み込む
   $array = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json_c),true); //jsonを配列に変換http://qiita.com/doima_/items/a8069fddf12ce10ede76 
   $array_length = count($array);
   $group_lenght = floor($array_length / 3); //切捨て割り算
   $group_excess = $array_length % 3;
   $ID_check=1;
   $lathai = join("," , $array); // $arrayという配列をカンマ区切りで展開して、$lathaiに代入
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"
<title></title>
<link rel="stylesheet" href="http://alphasis.info/library/javascript/jquery/ui/themes/base/jquery.ui.all.css">
<script type="text/javascript" src="http://alphasis.info/library/javascript/jquery/jquery-1.4.2.js"></script>
<script type="text/javascript" src="http://alphasis.info/library/javascript/jquery/ui/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" src="http://alphasis.info/library/javascript/jquery/plugin/jquery.cookie.js"></script>
</head>
<body>

<script>
jQuery( function() {

	
	//複数のリストを超えて並び替え
	jQuery( ".jquery-ui-sortable" ) . sortable( {
		connectWith: ".jquery-ui-sortable"
	} );
	jQuery( '.jquery-ui-sortable' ) . disableSelection();
	var array=jQuery('.jquery-ui-sortable').get;

	//リストの順番をURLに表示
	jQuery( '#submitSortable' ) . click( function() {
		
		var itemIDs_1 = '';
		var itemIDs_2 = ''; //並び替えた後データ
		var itemIDs_3 = '';
			
		jQuery( '#jquery-ui-sortable1 li' ) . map( function() {
				itemIDs_1 += jQuery( this ) .children( 'span' ) . text() + ',';
		} );
		jQuery( '#jquery-ui-sortable2 li' ) . map( function() {
				itemIDs_2 += jQuery( this ) .children( 'span' ) . text() + ',';
		} );
		jQuery( '#jquery-ui-sortable3 li' ) . map( function() {
				itemIDs_3 += jQuery( this ) .children( 'span' ) . text() + ',';
		} );

	} );
	//変更をクッキーに保存
	jQuery( '#jquery-ui-sortable1' ) . sortable();
	jQuery( '#jquery-ui-sortable1' ) . disableSelection();
        jQuery( '#jquery-ui-sortable1' ) . sortable( {
        	update: function( event, ui ) {
		var updateArray = jQuery( '#jquery-ui-sortable1' ) . sortable( 'toArray' ) . join( ',' );
        	jQuery . cookie( 'jquery-ui-sortable4', updateArray, { expires: 1 } );
        	}
        } );
	if( jQuery . cookie( 'jquery-ui-sortable4' ) ){
        var cookieValue = jQuery . cookie( 'jquery-ui-sortable4' ) . split( ',' ) . reverse();
        jQuery . each(
            cookieValue,
            function( index, value ){ jQuery( '#' + value ) . prependTo( '#jquery-ui-sortable1' ); }
        );
	}
	jQuery( '#jquery-ui-sortable2' ) . sortable();
	jQuery( '#jquery-ui-sortable2' ) . disableSelection();
        jQuery( '#jquery-ui-sortable2' ) . sortable( {
        	update: function( event, ui ) {
		var updateArray = jQuery( '#jquery-ui-sortable2' ) . sortable( 'toArray' ) . join( ',' );
        	jQuery . cookie( 'jquery-ui-sortable5', updateArray, { expires: 1 } );
        	}
        } );
	if( jQuery . cookie( 'jquery-ui-sortable5' ) ){
        var cookieValue = jQuery . cookie( 'jquery-ui-sortable5' ) . split( ',' ) . reverse();
        jQuery . each(
            cookieValue,
            function( index, value ){ jQuery( '#' + value ) . prependTo( '#jquery-ui-sortable2' ); }
        );
	}
	jQuery( '#jquery-ui-sortable3' ) . sortable();
	jQuery( '#jquery-ui-sortable3' ) . disableSelection();
        jQuery( '#jquery-ui-sortable3' ) . sortable( {
        	update: function( event, ui ) {
		var updateArray = jQuery( '#jquery-ui-sortable3' ) . sortable( 'toArray' ) . join( ',' );
        	jQuery . cookie( 'jquery-ui-sortable6', updateArray, { expires: 1 } );
        	}
        } );
	if( jQuery . cookie( 'jquery-ui-sortable6' ) ){
        var cookieValue = jQuery . cookie( 'jquery-ui-sortable6' ) . split( ',' ) . reverse();
        jQuery . each(
            cookieValue,
            function( index, value ){ jQuery( '#' + value ) . prependTo( '#jquery-ui-sortable3' ); }
        );
	}
	//resetボタン入力時クッキーを削除
	jQuery('#reset').click(function(){
		jQuery . cookie('jquery-ui-sortable4',null);
		jQuery . cookie('jquery-ui-sortable5',null);
		jQuery . cookie('jquery-ui-sortable6',null);
	})

} );
</script>


<style>
<!--
ul.jquery-ui-sortable {
	list-style-type: none;
	margin: 0 2px;
	padding: 2px;
	width: 15%;
	float: left;
	min-height: 1.5em;
	background-color: beige;
	border: solid 1px #606060;
}
ul.jquery-ui-sortable li {
	margin: 3px;
	padding: 0.3em;
	padding-left: 1em;
	font-size: 15px;
	font-weight: bold;
	cursor: move;
}
li.border-color-red {
	border-color: red;
}
li.border-color-blue {
	border-color: blue;
}
li.border-color-green {
	border-color: green;
}
-->
</style>
<div class="pure-u-7-24">
<li>グループA</li>
<ul id="jquery-ui-sortable1" class="jquery-ui-sortable">
<?php
	for($i=1;$i<=$group_lenght;$i++){
		$participant ="<li id=\"";
		$participant.=$ID_check;
		$participant.="\" class=\"ui-state-default border-color-red\">ID:<span>";
		$participant.=$ID_check;
		$participant.="</span></li>\n";
		echo $participant;
		$ID_check++;
	}
?>
</ul>
</div>
<div class="pure-u-7-24">
<li>グループB</li>
<ul id="jquery-ui-sortable2" class="jquery-ui-sortable">
<?php
	for($i=1;$i<=$group_lenght;$i++){
		$participant ="<li id=\"";
		$participant.=$ID_check;
		$participant.="\" class=\"ui-state-default border-color-blue\">ID:<span>";
		$participant.=$ID_check;
		$participant.="</span></li>\n";
		echo $participant;
		$ID_check++;
	}
?></ul>
</div>
<div class="pure-u-7-24">
<li>グループC</li>
<ul id="jquery-ui-sortable3" class="jquery-ui-sortable">
<?php
	for($i=1;$i<=($group_lenght + $group_excess);$i++){
		$participant ="<li id=\"";
		$participant.=$ID_check;
		$participant.="\" class=\"ui-state-default border-color-green\">ID:<span>";
		$participant.=$ID_check;
		$participant.="</span></li>\n";
		echo $participant;
		$ID_check++;
	}
?></ul>
</div>
<div align="center"　>
<div style="clear: both;"></div>
<p><input type="button" id="submitSortable" value="この並び順を送信">　　<button id="reset">クッキー消去</button>　　<input type="button" value="このページを再読込" onclick="location.reload();"></p>
<!--<script src="http://localhost/empty.js"></script>-->
</div>
</body>
</html>