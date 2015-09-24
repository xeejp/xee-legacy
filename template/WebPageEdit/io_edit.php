<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script> 
<link rel="stylesheet" type="text/css" href="popup_design.css" />
<link rel="stylesheet" type="text/css" href="design.css"/>
<link rel="stylesheet" type="text/css" href="CLEditor/jquery.cleditor.css" />
<script type="text/javascript" src="CLEditor/jquery.cleditor.min.js"></script>
<script type="text/javascript" src="function.js"></script>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<h1>Webページ編集</h1>
<h5>
    注意:変数は0~9で始まらない半角英数文字で記述してください。</br>
        また、変数内で使える文字はa~z,A~Z,$,_(アンダーバー)です。</br>
        変数内での改行は禁止されています。
</h5>
    
<textarea class="editor" id = "comment" name="comment" cols="60" rows="8"></textarea>
<div class="preview" id="target"></div>
<table id="hensu_add" class="pure-table">
    <thead>
        <tr>
            <th>変数追加ボタンリスト</th>
        </tr>
    </thead>
    <tbody id="tableBody_hensu_add">
        
    </tbody>
</table>

<div id="under">
    <button href="#popup1" class="popup_btn pure-button">新規変数追加</button>
    <div id="popup1" class="popup">
        <div class="popup_inner">
            <h4>新規変数追加</h4>
            <p>変数名</p>
            <input type="text" id="hensu_name" />
            <p>変数説明</p>
            <input type="text" id="hensu_desc" />
            <p>型</p>
            <select id="hensu_type">
                <option value="string">string</option>
                <option value="int">int</option>
                <option value="bool">bool</option>
            </select>
            <div>
                <button href="#decide_btn" class="decide_btn pure-button">決定</button>
                <button href="#close_btn" class="close_btn pure-button">閉じる</button>
            </div>
        </div>
    </div>
    <div id="popup2" class="popup">
        <div class="popup_inner">
            <h4>変数編集</h4>
            <p>変数名</p>
            <p id="hensu_name_edit"></p>
            <p>変数説明</p>
            <input type="text" id="hensu_desc_edit" />
            <p>型</p>
            <select id="hensu_type_edit">
                <option value="string">string</option>
                <option value="int">int</option>
                <option value="bool">bool</option>
            </select>
            <div>
                <button href="#decide_btn" class="decide_btn_edit pure-button">決定</button>
                <button href="#close_btn" class="close_btn_edit pure-button">閉じる</button>
            </div>
        </div>
    </div>

    <div id="overlay"></div>

    <!--<input type="submit" id="btn" value="決定"></input>-->

    <button id="btn" class="pure-button">決定</button>
    <h2>変数リスト</h2>
    <table id="list" class="pure-table pure-table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>変数名</th>
                <th>変数の説明</th>
                <th>変数の型</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            
        </tbody>
    </table>
</div>
