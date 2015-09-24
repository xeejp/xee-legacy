$(document).ready(function(){

// objects
var toolArea = $('#tool-area');
var editArea = $('#edit-area');

///// xflow initialization
// init
Xflow.init();

///// remove box
// text
toolArea.append($('<div>').text('Remove'));
// remove prefab
var removebox = Xflow.createPrefab({label: '<center>Drop here to remove</center>', receivable: false, senderNum: 0});
removebox.droppable({
	accept: '.node',
	drop: function(event, ui){
		if(ui.helper.hasClass("prefab")){
			ui.helper.remove();
			return;
		}
		var node = ui.draggable.data('wrapper');
		if(node.label === 'Start') return;
		node.destroy();
	}
});
toolArea.append(removebox);

///// prefab settings
/// editable
// text
toolArea.append($('<div>').text('Editable'));
toolArea.addClass('pure-form');
// editable prefab
var ePrefab = Xflow.createPrefab({receivable: true, senderNum: 0});
var inputBox = $(document.createElement('div'));
var iLabel      = $('<input>',{type: 'text',  placeholder: 'Conditions'});
var iSenderNum  = $('<input>',{type: 'text', value: '1'});
var iReceivable = $('<input>',{type: 'checkbox', checked: ''});
toolArea.append(ePrefab.append(inputBox.append('if\n',iLabel, iSenderNum, iReceivable)));
// inputBox settings
iLabel.css({width: (ePrefab.width() * 0.8) + 'px'});
iSenderNum.css({width: (ePrefab.width() * 0.5) + 'px'});
inputBox.css({
	position: 'absolute',
	display: 'inline',
	padding: '2px',
}).css({
	top : (ePrefab.height()/2 - inputBox.height()/2) + 'px',
	left: (ePrefab.width() /2 - inputBox.width() /2) + 'px',
});
// editable draggable
ePrefab.draggable({
	start:function(event,ui){
		iLabel.blur();
		iSenderNum.blur();
		iReceivable.blur();
		},
	helper: function(event, ui){
		var label = 'if'+'</br>'+iLabel.val();
		if(label === 'Start') label = 'dummy';
		var senderNum = parseInt(iSenderNum.val());
		if(senderNum < 0) senderNum = 0;
		if(senderNum > 5) senderNum = 5;
		var option = {
			label: label,
			senderNum: senderNum,
			receivable: iReceivable.prop('checked'),
		};
		$(event.target).data('prefab', option)
			.focus(); //fix: created empty box if focus input
		return Xflow.createPrefab(option);
	},
});

//for box
var forBox = Xflow.createPrefab({receivable:true,senderNum:1});
var inputBoxF = $(document.createElement('div'));
var forLabel	= $('<input>',{type: 'text',  placeholder: 'count'});
toolArea.append(forBox.append(inputBoxF.append('Repeat start',forLabel)));
//forBox settings
forLabel.css({width: (forBox.width() * 0.8) + 'px'});
inputBoxF.css({
	position: 'absolute',
	display: 'inline',
	padding: '2px',
}).css({
	top : (forBox.height()/2 - inputBoxF.height()/2) + 'px',
	left: (forBox.width() /2 - inputBoxF.width() /2) + 'px',
});

forBox.draggable({
	start:function(event,ui){
		forLabel.blur();
		},
	helper:function(event,ui){
	var label = 'Repeat start'+'</br>'+forLabel.val() +'times';
	if(label === 'Start') label = 'dummy';
	var option = {
		label : label
	};
	$(event.target).data('prefab', option)
			.focus(); //fix: created empty box if focus input
		return Xflow.createPrefab(option);
	},
});


//repeat end set
var RepeatEnd= Xflow.createPrefab({label: 'Repeat end'   , senderNum: 1, receivable: true});
toolArea.append(RepeatEnd.append());
RepeatEnd.draggable({
		helper:'clone',
		});
//page jump
var transition = Xflow.createPrefab({senderNum: 0});
var inputBoxT = $(document.createElement('div'));
var transitionLabel = $('<input>',{type: 'text',  placeholder: 'Page name'});
toolArea.append(transition.append(inputBoxT.append('transition to',transitionLabel)));

transitionLabel.css({width: (inputBoxT.width() * 0.8) + 'px'});
inputBoxT.css({
	position: 'absolute',
	display: 'inline',
	padding: '2px',
}).css({
	top : (transition.height()/2 - inputBoxT.height()/2) + 'px',
	left: (transition.width() /2 - inputBoxT.width() /2) + 'px',
});
transition.draggable({
	start: function(event,ui){
		transitionLabel.blur();
	},
	helper:function(event,ui){
	var label = 'transition to'+'</br>'+transitionLabel.val();
	var SenderNum= 0 ;
	if(label === 'Start') label = 'dummy';
	var option = {
		label : label,
		senderNum : '0',
	};
	$(event.target).data('prefab', option)
			.focus(); //fix: created empty box if focus input
		return Xflow.createPrefab(option);
	},
});



/// prepared prefabs
// text
toolArea.append($('<div>').text('Functions'));

// add list
var prefabList = {};
//transition to result
prefabList['transitionR']  = Xflow.createPrefab({label: 'transition to result page' , senderNum: 0, receivable: true});
//add
prefabList['a']  = Xflow.createPrefab({label: 'user matching' , senderNum: 1, receivable: true});
prefabList['b']  = Xflow.createPrefab({label: 'trade' , senderNum: 1, receivable: true});
prefabList['c']  = Xflow.createPrefab({label: 'price check' , senderNum: 1, receivable: true});

// add prefab
$.each(prefabList, function(name, obj){
	toolArea.append(obj.addClass('prepared-prefab'));
});
toolArea.children('.prepared-prefab').draggable({helper: 'clone'});

///// xflow settings
// add start
Xflow.createNode({label: 'Start' , senderNum: 1, receivable: false});
// set I/O
var input = $('#data');
$('#export').click(function(){
	input.val(Xflow.exportData());
});
$('#import').click(function(){
	if(confirm("Current Status will be lost.\nAre you sure ?"))
	Xflow.importData(input.val());
});

//

});