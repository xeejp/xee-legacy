<div class="container">

<div class="page-header">
    <div align="center"><h1>経済実験へようこそ！！</h1></div>
</div>

<!-- Interactive Login - START -->
<div class="container">
    <div class="row colored">
        <div id="contentdiv" class="contcustom">
            <span class="fa fa-user bigicon"></span>
            <h2>Login</h2>
            <div>
				<form action="top" method="post">
                <input name="password" type="text" placeholder="実験番号" onkeypress=";">
                <input name="name" type="text" placeholder="ID/学籍番号" onkeypress=";">
                <button id="button1" class="btn btn-default wide hidden"><span class="fa fa-check med"></span></button>
                <input type="submit" id="lock1" class="fa medhidden redborder">　実験に参加！</span>
               　<span id="lock1" class="fa medhidden redborder">　実験に参加！</span>
                <input type="hidden" name="{token_name}" value="{TOKEN}">
			   </form>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="template/index.css" />

<!-- Interactive Login - END -->
<div align="right" valign="bottom">
<button type="button" class="btn btn-default">実験作成</button>
</div>
</div>
