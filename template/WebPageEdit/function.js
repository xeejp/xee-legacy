$(document).ready(function() {
    var Hensu = function(name,desc,type){
        this.name = name;
        this.desc = desc;
        this.type = type;

        this.getName = function(){return this.name;};
        this.getDesc = function(){return this.desc;};
        this.getType = function(){return this.type;};
        this.setName = function(name){this.name = name;};
        this.setDesc = function(desc){this.desc = desc;};
        this.setType = function(type){this.type = type;};
    };
    var hensu_array = new Object();
    var enterFlag = false;
    textRange = null;
    //CLEditorをtextareaに適用
    var editor = $(".editor").cleditor({
        width: 400,
        height:800,
    })[0];

    //文字挿入関数
    String.prototype.splice = function(idx, rem, s) { 
        return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
    };

    //第二引数(配列)内に第一引数(文字列)と同じものがないか調べる
    function checkListSame(str,use_hensu)
    {
        if(use_hensu.indexOf(str) < 0){
            return false;
        }
        return true;
    }
    //(未実装)変数名の中でenterを押したときにalertをだす(予定)
    $(document).on("keypress", "#comment",function(e){
        if(e.keyCode == 13 && enterFlag == false){
            enterFlag = true;
            alert("注意！変数内での改行はしないようにお願いします。");
        }
    });

    function insertHtml(str){
        editor.execCommand('inserthtml', '{' + str + "}", false);
    }

    //変数をチェックする
    function checkHensu(override_html){
        var use_hensu = new Array();
        var hensu_count = 0;
        var str = '';
        var len = override_html.length;
        var list_len = $('#tableBody').length;
        for(var i = 0;i < list_len;i++){
            $('tbody#tableBody_hensu_add').empty();
            $('tbody#tableBody').empty();
        }
        for( var i = 0; i < len; i ++){
            if(override_html.charAt(i) == '{' && override_html.charAt(i - 1) != '/'){
                for( var j = i + 1;j < len;j ++){
                    if(override_html.charAt(j) == ' ' || override_html.charAt(j) == '' || override_html.charAt(j) == '　' || override_html.charAt(j) == '"' || override_html.charAt(j) == '¥' || override_html.charAt(j) == "'"){//修正必要
                                alert("その入力は禁止されています : " + override_html.charAt(j));
                    }else{
                        if(override_html.charAt(j) == '}'){
                            if(override_html.charAt(j - 1) != '/'){
                                //リストに変数名strが存在するか確認する
                                //もしあったら、そのまま。
                                //なかったら、リストに自動追加。ただし、説明等は自分で編集する必要あり(未実装)
                                
                                //H27_9_22 まず使われている変数名を取得し、すでにリストアップされている分の変数と比較する。合わないものは新規作成or削除をしてからリストアップする。
                                var isSame = false;
                                isSame = checkListSame(str,use_hensu);

                                if(!isSame){
                                    use_hensu.push(str);
                                }
                            }
                            break;
                        }
                    }
                    str += override_html.charAt(j);
                }
                str = '';
            } 
        }
        for(var i = 0;i < use_hensu.length;i++){
            if(hensu_array[use_hensu[i]] == null){//hensu_arrayにuse_hensu[i]が存在しない
                hensu_array[use_hensu[i]] = new Hensu(use_hensu[i],"","");
                break;
            }
        }
        for(var i = 0;i < use_hensu.length;i++){
            if(use_hensu.indexOf(hensu_array[use_hensu[i]].getName()) < 0){//use_hensuにhensu_array[use_hensu[i]].getName()が存在しない
                delete hensu_array[use_hensu[i]];
                hensu_length--;
            }
        }
        for(var i = 0;i < use_hensu.length;i++){
            var name = hensu_array[use_hensu[i]].getName();
            var desc = hensu_array[use_hensu[i]].getDesc();
            var type = hensu_array[use_hensu[i]].getType();
            var e = document.getElementById('tableBody');
            var elemLi = document.createElement('tr');
            elemLi.id = name;
            e.appendChild(elemLi);
            var eTr = document.getElementById(name);
            var elemThSharp = document.createElement('th');
            var elemThName = document.createElement('th');
            var elemThDesc = document.createElement('th');
            var elemThType = document.createElement('th');
            elemThSharp.textContent = " ";
            elemThSharp.id="buttonEdit" + name;
            elemThName.textContent = name;
            elemThName.id="TableName" + name;
            elemThDesc.textContent = desc;
            elemThDesc.id="TableDesc" + name;
            elemThType.textContent = type;
            elemThType.id="TableType" + name;
            eTr.appendChild(elemThSharp);
            eTr.appendChild(elemThName);
            eTr.appendChild(elemThDesc);
            eTr.appendChild(elemThType);
            var eThLeft = document.getElementById("buttonEdit" + name);
            var elemButtonEdit = document.createElement('button');
            elemButtonEdit.textContent = "編集";
            elemButtonEdit.value = "#popup2";
            elemButtonEdit.className = "pure-button popup_btn_edit";
            elemButtonEdit.id="a" + name;
            eThLeft.appendChild(elemButtonEdit);
            
            //H27_9_23 定義済みの変数を追加するボタンをつくる

            var e = document.getElementById('tableBody_hensu_add');
            var elemLi = document.createElement('tr');
            elemLi.id = "tr" + name;
            e.appendChild(elemLi);
            var eTr = document.getElementById("tr" + name);
            var elemTh = document.createElement('th');
            elemTh.id="th" + name;
            eTr.appendChild(elemTh);
            var eTh = document.getElementById("th" + name);
            var elemBtn = document.createElement('button');
            elemBtn.textContent = name;
            elemBtn.id= "button" + name;
            elemBtn.className="pure-button btn_add";                
            if(hensu_array[name].getDesc() != ""){
                switch(hensu_array[name].getType()){
                    case "string":
                        elemBtn.style.backgroundColor = '#FA8072';
                        break;
                    case "int":
                        elemBtn.style.backgroundColor = '#87CEFA';
                        break;
                    case "bool":
                        elemBtn.style.backgroundColor = '#98FB98';
                        break;
                    default:
                        
                }
            }
            eTh.appendChild(elemBtn);
        }
    }

    //左側入力フォームの内容を右側に表示する内容に加工
    function processHtml(override_html){
        var str = '';
        var span_str = '"border-style: solid;border-width: 1px;background-color: #0FF;"';
        var flag = false;
        var len = override_html.length;
        for( var i = 0; i < len; i ++){
            if(override_html.charAt(i) == '/'){
                if(override_html.charAt(i + 1) == '/'){
                    str += '/';
                    i += 1;
                    continue;
                }
                if(override_html.charAt(i + 1) == '{'){
                    str += '{';
                    i += 1;
                    continue;
                }
                if(override_html.charAt(i + 1) == '}'){
                    str += '}';
                    i += 1;
                    continue;
                }
                str += '/';
                continue;
            }
            if(override_html.charAt(i) == '{' && flag == false){
                for( var j = i;j < len;j ++){
                    if(override_html.charAt(j) == '}'){
                        if(override_html.charAt(j - 1) != '/'){
                            str += '<span style=' + span_str + '>';
                            flag = true;
                            break;
                        }else{
                            break;
                        }
                    }
                }
                continue;
            }
            if(override_html.charAt(i) == '}' && flag == true){
                flag = false;
                str += '</span>';
                continue;
            }
            str += override_html.charAt(i);
        }
        return str;
    }

    var setTable = function(arr){
        var eTableDesc = document.getElementById('TableDesc' + arr["hensu_name"]);
                    var eTableType = document.getElementById('TableType' + arr["hensu_name"]);
                    eTableDesc.textContent = arr["hensu_desc"];
                    eTableType.textContent = arr["hensu_type"];

    }

    var refreshText = function() {
        // cleditorを取得し、編集内容を一旦確定。
        // 表示上、隠れているinputareaの内容を更新する。
        var editor = $("#comment").cleditor()[0];
        editor.updateTextArea();

        // inputareaの内容を取得し、divに表示
        var override_html = $("#comment").val();
        //var processed0_html = checkHensu(override_html);
        checkHensu(override_html);
        var processed1_html = processHtml(override_html);
        $("#target").html(processed1_html);
    };

    // keyupを取得し、文字入力が行われたタイミングで再描画
    $( $(".cleditorMain iframe")[0].contentWindow.document ).bind('keyup', refreshText);

    // もうひとつ、テキストのハイライトなどが行われた時のイベントを取得するため、
    // 入力領域にフォーカスが戻ってくるイベントも監視しておく
    $( $(".cleditorMain iframe").contentWindow ).focus(refreshText);

    //「追加」をクリックした際に出てくるポップアップ関連
    (function($){
        $(function(){
            $(document)
                .on('click', '.popup_btn', function(){
                     var $popup = $($(this).attr('href'));

                     // ポップアップの幅と高さからmarginを計算する
                     var mT = ($popup.outerHeight() / 2) * (-1) + 'px';
                     var mL = ($popup.outerWidth() / 2) * (-1) + 'px';

                     // marginを設定して表示
                     $('.popup').hide();
                     $popup.css({
                         'margin-top': mT,
                         'margin-left': mL
                         }).show();
                     $('#overlay').show();

                     return false;
                     })
                .on('click', '.close_btn, #overlay', function(){
                $('.popup, #overlay').hide();
                    return false;
                })
                .on('click', '.decide_btn', function(){
                    var arr = {"hensu_name":document.getElementById("hensu_name").value,
                               "hensu_desc":document.getElementById("hensu_desc").value,
                               "hensu_type":document.getElementById("hensu_type").value};

                    var use_hensu = new Array();
                    for(var j in hensu_array){
                        use_hensu.push(hensu_array[j].getName());
                    }
                    if(checkListSame(arr["hensu_name"],use_hensu)){
                        alert("その変数名はすでに使用されています");
                        return false;
                    }
                    hensu_array[arr["hensu_name"]] = new Hensu(arr["hensu_name"],arr["hensu_desc"],arr["hensu_type"]);
                    $('.popup, #overlay').hide();
                    insertHtml(arr["hensu_name"]);
                    alert("変数" + arr["hensu_name"] + "を追加しました");
                    refreshText();
                    var eName = document.getElementById('hensu_name');
                    eName.value = "";
                    var eDesc = document.getElementById('hensu_desc');
                    eDesc.value = "";
                    return true;
                });
        });
    })(jQuery);

    //「編集」をクリックした際に出てくるポップアップ関連
    (function($){
        $(function(){
            $(document)
                .on('click', '.popup_btn_edit', function(){
                     var $popup = $($(this).attr('value'));

                     // ポップアップの幅と高さからmarginを計算する
                     var mT = ($popup.outerHeight() / 2) * (-1) + 'px';
                     var mL = ($popup.outerWidth() / 2) * (-1) + 'px';

                     // marginを設定して表示
                     $('.popup').hide();
                     $popup.css({
                         'margin-top': mT,
                         'margin-left': mL
                         }).show();
                     $('#overlay').show();
                         var eSelect = $(this).attr('id');
                         eSelect = eSelect.substr(1);
                         var eName = document.getElementById("hensu_name_edit");
                         var eDesc = document.getElementById("hensu_desc_edit");
                         var eType = document.getElementById("hensu_type_edit");
                         eName.textContent = hensu_array[eSelect].getName();
                         eDesc.value = hensu_array[eSelect].getDesc();
                         eType.value = hensu_array[eSelect].getType();
                         return false;
                     })
                .on('click', '.close_btn_edit, #overlay', function(){
                    $('.popup, #overlay').hide();
                    return false;
                })
                .on('click', '.decide_btn_edit', function(){
                    var arr = {"hensu_name":document.getElementById("hensu_name_edit").textContent,
                               "hensu_desc":document.getElementById("hensu_desc_edit").value,
                               "hensu_type":document.getElementById("hensu_type_edit").value};
                  
                    hensu_array[arr["hensu_name"]].setDesc(arr["hensu_desc"]);
                    hensu_array[arr["hensu_name"]].setType(arr["hensu_type"]);
                    $('.popup, #overlay').hide();
                    alert("変数" + arr["hensu_name"] + "を編集しました");
                    refreshText();
                    setTable(arr);
                    var eDesc = document.getElementById('hensu_desc_edit');
                    eDesc.value = "";
                    
                    return true;
                });
        });
    })(jQuery);

    //H27_9_23 右の変数追加ボタン関連
    (function($){
        $(function(){
            $(document)
                .on('click', '.btn_add', function(){
                     var eSelect = $(this).attr('id');
                     eSelect = eSelect.substr(6);
                     insertHtml(eSelect);
                     refreshText();
                });
        });
    })(jQuery);  

});
