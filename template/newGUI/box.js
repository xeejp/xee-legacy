/*送るデータ方式
[{"code":function,"val":[valA,valB],"internal":[]}{"code":.......}]
*/
function create(obj){
	if(obj.name == null || obj.value == null) return null;
	return make[obj.name].decode.apply($(),obj.value);
}

$(function(){
//比較符号
var sign = ['=','<','>','≠'];
//ユーザー定義関数の読み込み
var variable=['aaa','123','abc'];
/*$.ajax({
	async :false,
	url:'test.txt',
	success: function(data){
		variable = data.split(/\r\n|\r|\n/);
	},
});
*/
make = {
	rpt_t :{	//回数指定繰り返し
		encode : function(obj){
			    return (null);
		},
		decode : function(num){
			    return this.add(input_make('number',num)).add('<a>回繰り返す</a>');
		},
	},

	rpt_c :{	//条件指定繰り返し
		encode : function(obj){
			    return (null);
		},	
		decode : function(v_var,v_sign,v_text){
			    return this.add(select_make(variable,v_var))
				       .add(select_make(sign,v_sign))
				       .add(input_make('text',v_text)).add('<a>まで繰り返す</a>');
		},
	},

}

//input生成関数
function input_make(type,val){
	var box = $('<input>').attr({type: type,value:val});
	if(type=='number') box.attr({min:0});
	return box;
}

//select生成関数
function select_make(options,val){
	var box = $('<select>');
	$.each(options,function(i,options){
		if(options==val){box.append($('<option selected>').html(options));}
		else{box.append($('<option>').html(options));}
	});
	return box;
}

});