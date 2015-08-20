$(document).ready(function() {
    $(".editor").cleditor({
        width: 400,
        height:800,
    });
    String.prototype.splice = function(idx, rem, s) {
        return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
    };

    function processHtml(override_html){
        var str = '';
        var span_str = '"border-style: solid;border-width: 1px"';
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
                str += override_html.charAt(i);
                continue;
            }
            if(override_html.charAt(i) == '}' && flag == true){
                flag = false;
                str += override_html.charAt(i);
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
        var processed_html = processHtml(override_html);
        $("#target").html(processed_html);
    };

    // keyupを取得し、文字入力が行われたタイミングで再描画
    $( $(".cleditorMain iframe")[0].contentWindow.document ).bind('keyup', refreshText);

    // もうひとつ、テキストのハイライトなどが行われた時のイベントを取得するため、
    // 入力領域にフォーカスが戻ってくるイベントも監視しておく
    $( $(".cleditorMain iframe").contentWindow ).focus(refreshText);

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
                $('.popup, #overlay').hide();
                var arr = {"hensu_name":document.getElementById("hensu_name").value,
                           "hensu_desc":document.getElementById("hensu_desc").value,
                           "hensu_type":document.getElementById("hensu_type").value};
                var e = document.getElementById('list');
                var elemLi = document.createElement('li');
                elemLi.textContent = "変数名 : " + arr["hensu_name"] + "\n変数の説明 : " + arr["hensu_desc"] + "\n変数の型 : " + arr["hensu_type"];
                elemLi.className = "listElem";
                e.appendChild(elemLi);
                var v = $(".editor").val();
                $(".editor").val(v + '{' + arr["hensu_name"] + '}').blur();
                alert("変数" + arr["hensu_name"] + "を追加しました");
                refreshText();
                return true;
            });
        });
    })(jQuery);
});
