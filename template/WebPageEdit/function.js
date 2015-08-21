$(document).ready(function() {
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

    //リスト内に引数と同じ文字列がないか調べる(現在は<ul id=list>,<li id=str>ってかんじ)
    function checkListSame(str)
    {
        var lis = $('ul#list').find('li#' + str);
            if(lis.length > 0){
                return true;
            }
        return false;
    }
    //(未実装)変数名の中でenterを押したときにalertをだす(予定)
    $().keypress(function (e){
        if(e.which == 13){
             alert("その入力は禁止されています : " + override_html.charAt(j));
        }
    });

    //変数をチェックする
    function checkHensu(override_html){
        var str = '';
        var len = override_html.length;
        var list_len = $('#list').length;
        for(var i = 0;i < list_len;i++){
            $('ul#list').empty();
        }
        for( var i = 0; i < len; i ++){
            if(override_html.charAt(i) == '{' && override_html.charAt(i - 1) != '/'){
                for( var j = i + 1;j < len;j ++){
                    if(override_html.charAt(j) == ' ' || override_html.charAt(j) == '' || override_html.charAt(j) == '  ' || override_html.charAt(j) == '"' || override_html.charAt(j) == '¥' || override_html.charAt(j) == "'"){
                                alert("その入力は禁止されています : " + override_html.charAt(j));
                    }else{

                        if(override_html.charAt(j) == '}'){
                            if(override_html.charAt(j - 1) != '/'){
                                //リストに変数名strが存在するか確認する
                                //もしあったら、そのまま。
                                //なかったら、リストに自動追加。ただし、説明等は自分で編集する必要あり(未実装)
                                var isSame = false;
                                var arr = {"hensu_name":str,
                                    "hensu_desc":document.getElementById("hensu_desc").value,
                                    "hensu_type":document.getElementById("hensu_type").value};
                                var e = document.getElementById('list');
                                var elemLi = document.createElement('li');
                                elemLi.textContent = "変数名 : " + arr["hensu_name"] + " 変数の説明 : " + arr["hensu_desc"] + " 変数の型 : " + arr["hensu_type"];
                                elemLi.className = "listElem";
                                elemLi.id = arr["hensu_name"];
                                elemLi.value = arr["hensu_name"];
                                isSame = checkListSame(arr["hensu_name"]);
                                /*var list_len = $('#list').length;
                                var findUl = document.getElementById('list');
                                    findLi = findUl.children;
                                for(var i = 0;i < list_len;i++){
                                    if(findLi[i]. == arr["hensu_name"]){
                                        isSame = true;
                                    }
                                }*/
                                if(!isSame)e.appendChild(elemLi);
                            }
                            break;
                        }
                    }
                    str += override_html.charAt(j);
                }
                str = '';
            } 
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
            .on('click', '.decide_btn, #overlay', function(){
                var arr = {"hensu_name":document.getElementById("hensu_name").value,
                           "hensu_desc":document.getElementById("hensu_desc").value,
                           "hensu_type":document.getElementById("hensu_type").value};
                var e = document.getElementById('list');
                var elemLi = document.createElement('li');
                elemLi.textContent = "変数名 : " + arr["hensu_name"] + " 変数の説明 : " + arr["hensu_desc"] + " 変数の型 : " + arr["hensu_type"];
                elemLi.className = "listElem";
                if(checkListSame(arr["hensu_name"])){
                    alert("その変数名はすでに使用されています");
                    return false;
                }

                $('.popup, #overlay').hide();
                e.appendChild(elemLi);
                var v = $(".editor").val();
                editor.execCommand('inserthtml', '{' + arr["hensu_name"] + "}", false);
                //$(".editor").val(v + '{' + arr["hensu_name"] + '}').blur();
                alert("変数" + arr["hensu_name"] + "を追加しました");
                refreshText();
                return true;
            });
        });
    })(jQuery);
});
