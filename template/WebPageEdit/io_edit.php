<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script> 
<link rel="stylesheet" type="text/css" href="popup_design.css" />
<link rel="stylesheet" type="text/css" href="design.css"/>
<link rel="stylesheet" type="text/css" href="CLEditor/jquery.cleditor.css" />
<script type="text/javascript" src="CLEditor/jquery.cleditor.min.js"></script>
<script type="text/javascript" src="function.js"></script>-->
<h1>Webページ編集</h1>
    
<textarea class="editor" id = "comment" name="comment" cols="60" rows="8"></textarea>
<div class="preview" id="target"></div>

<div id="under">
    <a href="#popup1" class="popup_btn">追加</a>

    <div id="popup1" class="popup">
        <div class="popup_inner">
            <h4>変数追加</h4>
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
                <a href="#decide_btn" class="decide_btn">決定</a>
                <a href="#close_btn" class="close_btn">閉じる</a>
            </div>
        </div>
    </div>
    <div id="overlay"></div>

    <!--<input type="submit" id="btn" value="決定"></input>-->

    <button id="btn">決定</button>
    <h2>変数リスト<h2>
    <ul id="list">
        
    </ul>
</div>
