$(function(){
/*
// 使い方
// (1) newする
var cl = new CodeLitter();
// (2) appendする
$('#code_litter').append(cl);
// (3) root(一番上の階層)を取得する
var list = cl.getRoot();
// (4) root(list)に命令を追加
var item = list.addItem('命令文');
// (5) 文字列の書き換え
item.code('<input type="text" value="printf('書き込み');" />');
// (6) (必要があれば)命令を展開可能にする
var list2 = item.addList();
// おわり
*//*
// JSONの巻
// (1) 書き出し
var json = cl.JSON();
// (2) 読み込み
cl.JSON(json);
*/

// プレハブ
//ツールボックス作成
var pcl = new CodeLitter();
pcl.appendTo('#code_litter');
var prt = pcl.getRoot();

//繰り返し系ツールの追加
prt.append('繰り返し');
prt.addItem().data('foldable', true).children('.code').append(create({name:'rpt_t',value:[]}));
prt.addItem().data('foldable', true).children('.code').append(create({name:'rpt_c',value:[]}));

//ページ遷移系ツールの追加
prt.append('ページ遷移');
prt.addItem('<input type="text">のページへ移動');
prt.addItem('結果のページへ移動');

//ツールの設定
prt.sortable('destroy').css('position', 'static');
prt.children('.item').draggable({
    helper: function(event, ui){ return $(event.target).clone(); },
    connectToSortable: '.list',
    drag: function (event, ui) {
        ui.helper.css({width: '', height: ''});
    },
    stop: function (event, ui) {
        var item = ui.helper.asCL('item');
        if ($(event.target).data('foldable'))
            item.addList();
        ui.helper.css({width: '', height: ''});
    },
    helper:'clone',
});

//ゴミ箱
var tcl = new CodeLitter();
tcl.appendTo('#code_litter');
var trt = tcl.getRoot();
trt.append('削除');
trt.sortable('destroy').css('position', 'static');
trt.droppable({
    tolerance: "pointer",
    drop: function(event, ui) {
	if($(ui.draggable).parent('*').hasClass('ui-sortable')){
	    ui.draggable.remove();
	}
    },
});


// 本体
var cl = new CodeLitter();
$('#code_litter').append(cl);
var root = cl.getRoot();
root.append('編集エリア');
// json サンプル
var dataStr = $(document.createElement('input'));
var strEnc = $(document.createElement('button')).html('Load').click(function(){ dataStr.val(cl.JSON()); });
var strDec = $(document.createElement('button')).html('Write').click(function(){ cl.JSON(dataStr.val()); });
var jsonIO = $(document.createElement('div')).append(dataStr, strEnc, strDec);
$('body').prepend(jsonIO);

// debug--
//$('*').css({border: 'solid 1px'});
// --debug

});