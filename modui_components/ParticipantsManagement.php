<?php
class ParticipantsManagement extends ModUIComponent{
    private $_con;
    public function __construct($con){
        $this->_con = $con;
    }
    public function get_template_name($name){
        return $name;
    }
    public function get_templates($name){
        $template = <<<TMPL
		    <div class="row colored">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="left" id="box" ondragover="f_dragover(event)" ondrop="f_drop_l(event)">
				<p align="center">参加者</p>
				{each participants}
					{if active}
					<div id="{id}" draggable='true'ondragstart='f_dragstart(event)'><p>{name}</p></div>
						{/if}
					{/each}
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6">
		<div class="right" id="box" ondragover="f_dragover(event)" ondrop="f_drop_r(event)">
			<p align="center">不参加者</p>
					{each participants}
					{if active}{else}
					<div id="{id}" draggable='true'ondragstart='f_dragstart(event)'><p>{name}</p></div>
						{/if}
					{/each}
			<div align="right">
				<div class="controls">
					<input type="button" value="確認" id="{_name}">
				</div>
			</div>
			
		</div>
		</div>
	
	</div>

TMPL;
        return [get_template_name($name) => $template];
    }
    public function get_values($name){
        return ['value' => $this->_con->get('a')];
    }
    public function get_scripts($name){
        return ['value' => <<<JS
	function(selector){
	var l=0;
	var r=0;
	for(l=0;l<l_elm.length;l++){//r_elmとl_elmの被りをなくす
			for(r=0;r<r_elm.length;r++){
				if(l_elm[l] == r_elm[r]){
					l_elm.splice(l,1);
					r_elm.splice(r,1);
					r--;
					l--;
				}
			}
		}	
		
		return [l_elm,r_elm];}
JS
		, 'event' => 'function(selector, update){$(document).on("click", "#" + selector , update);}',
		'other' => <<<JS
		    jQuery(function ($) {
        function check_values() {
            if ($("#username").val().length != 0 && $("#password").val().length != 0) {
                $("#button1").removeClass("hidden").animate({ left: '250px' });
                $("#lock1").addClass("hidden").animate({ left: '250px' });
            }
        }
    });
	
//空の配列宣言
var l_elm =[];
var r_elm =[];
/***** ドラッグ開始時の処理 *****/
function f_dragstart(event){
  //ドラッグするデータのid名をDataTransferオブジェクトにセット
  event.dataTransfer.setData("text", event.target.id);
}

/***** ドラッグ要素がドロップ要素に重なっている間の処理 *****/
function f_dragover(event){
  //dragoverイベントをキャンセルして、ドロップ先の要素がドロップを受け付けるようにする
  event.preventDefault();
}

/***** leftドロップ時の処理 *****/
function f_drop_l(event){
  //ドラッグされたデータのid名をDataTransferオブジェクトから取得。
  var id_name = event.dataTransfer.getData("text"); 
  //id名からドラッグされた要素を取得
  var drag_elm =document.getElementById(id_name);
  //配列に追加
  l_elm.push(id_name);
  //ドロップ先にドラッグされた要素を追加
  event.currentTarget.appendChild(drag_elm);
  //エラー回避のため、ドロップ処理の最後にdropイベントをキャンセルしておく
  event.preventDefault();
}
/***** rightドロップ時の処理 *****/
function f_drop_r(event){
　　//空の配列宣言
  //var out_name = [];
  //ドラッグされたデータのid名をDataTransferオブジェクトから取得。
  var id_name = event.dataTransfer.getData("text");
  //id名からドラッグされた要素を取得
  var drag_elm =document.getElementById(id_name);
  //配列に追加
  r_elm.push(id_name);
  //ドロップ先にドラッグされた要素を追加
  event.currentTarget.appendChild(drag_elm);
  //エラー回避のため、ドロップ処理の最後にdropイベントをキャンセルしておく
  event.preventDefault();
}
JS
];
    }
    public function input($name, $value){
    }
}