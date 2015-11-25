<?php
class ParticipantsGroup extends ModUIComponent{
    private $_con;
    public function __construct($con){
        $this->_con = $con;
        $this->_url = _URL;
    }
    public function get_template_name($name){
        return $name;
    }
    public function get_templates($name){
        $template = <<<TMPL
<div>
{each participants_groups}
    <div class="pure-u-7-24" style="display: inline;">
        <li>グループ{group_id}</li>
        <ul id="jquery-ui-sortable{group_id}" class="jquery-ui-sortable">
            {each participants}
            <li id="{id}" class="ui-state-default border-color-red">ID:<span>{id}</span></li>
            {/each}
        </ul>
    </div>
{/each}
</div>
<div align="center">
    <div style="clear: both;"></div>
    <p><input type="button" id="submitSortable" value="この並び順を送信">　　<button id="reset">クッキー消去</button>　　<input type="button" value="このページを再読込" onclick="location.reload();"></p>
</div>
TMPL;
        return [$this->get_template_name($name) => $template];
    }
    public function get_values($name){
        return [
            'participants_groups' => [
                [
                    'group_id' => 0,
                    'participants' => [
                        [ 'id' => 0 ],
                        [ 'id' => 1 ],
                        [ 'id' => 2 ],
                        [ 'id' => 3 ],
                        [ 'id' => 4 ],
                        [ 'id' => 5 ],
                    ]
                ],
                [
                    'group_id' => 1,
                    'participants' => [
                        [ 'id' => 10 ],
                        [ 'id' => 11 ],
                        [ 'id' => 12 ],
                        [ 'id' => 13 ],
                        [ 'id' => 14 ],
                        [ 'id' => 15 ],
                    ]
                ],
                [
                    'group_id' => 2,
                    'participants' => [
                        [ 'id' => 20 ],
                        [ 'id' => 21 ],
                        [ 'id' => 22 ],
                        [ 'id' => 23 ],
                        [ 'id' => 24 ],
                        [ 'id' => 25 ],
                    ]
                ]
            ]
        ];
    }
    public function get_scripts($name){
        return [
            'value' => <<<JS
function(selector) {
    return [[1, 2, 3]];
    return jQuery.cookie();
}
JS
,           'event' => 'function(selector, update){$(document).on("click", "#" + selector , update);}',
            'other' => <<<JS
</script>
<link rel="stylesheet" href="{$this->_url}js/jquery-ui.min.css">
<script type="text/javascript" src="{$this->_url}js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="{$this->_url}js/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$this->_url}js/jquery.cookie.js"></script>
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
<script>
jQuery( function() {
    //複数のリストを超えて並び替え
    var gropuArray = jQuery( ".jquery-ui-sortable" )
        .sortable({
          connectWith: ".jquery-ui-sortable"
        })
        .disableSelection()
        .get;
    //リストの順番をURLに表示
    jQuery( '#submitSortable' )
        .click( function() {
            var itemIDs = jQuery( ':li[id^=jquery-ui-sortable]' )
                .map( function(i) {
                    return jQuery( this ) .children( 'span' ) . text() + ',';
                });
        });
    //変更をクッキーに保存
    jQuery( ':li[id^=jquery-ui-sortable]' )
        .disableSelection()
        .sortable( {
            update: function( event, ui ) {
                var sortable = $(event.target);
                var updateArray = sortable . sortable( 'toArray' ) . join( ',' );
                jQuery . cookie( sortable.attr('id'), updateArray, { expires: 1 } );
            }
        })
        .each( function() {
            var cookieName = $(this).attr('id');
            if(! jQuery.cookie(cookieName) ) return true;
            var cookieValue = jQuery . cookie( cookieName ) . split( ',' ) . reverse();
            jQuery . each( cookieValue,
                function( index, value ){
                    jQuery( '#' + value ) . prependTo( '#' . cookieName );
                }
            );
        });
    //resetボタン入力時クッキーを削除
    jQuery('#reset').click(function(){
        jQuery.each( jQuery.cookie(),
            function(key, val) {
                jQuery.cookie(key, null);
            }
        );
    });
} );
JS
        ];
    }
    public function input($name, $value){
    }
}
