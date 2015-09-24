;var Xflow;
(function($){
/*    data    */
var prop = {
	env: {
		ver: '0.00',
	},
	flow: {},
};
/*    main methods    */
Xflow = {
	// initialization
	init: function(){
		prop.flow = $("#xflow").empty();
		prop.flow.droppable({
			accept: '.prefab',
			drop: function(event, ui){
				var prefab = ui.draggable;
				var offset = ui.helper.offset();
				var node = Xflow.createNode(prefab.data('prefab'));
				node.get().offset(offset);
				node.resetPosition();
				Xflow.xflowResize();
				//node.draggable.stop({}, {helper: node.get()});
				ui.helper.remove();
			},
		});
	},
	get: function(){
		return prop.flow;
	},
	// create prefab objects
	createPrefab: function(option){
		var obj = Xflow.createNode(option).get();
		var prefab = obj.clone()
			.addClass('prefab')
			.data('prefab', option);
		obj.remove();
		return prefab;
	},
	// create and add node
	createNode: function(option){
		// set defalut option
		var def = {
			label: '',
			receivable: true,
			senderNum: 1,
		};
		var opt = $.extend({}, def, option);
		// node
		var node = new Node(opt);
		prop.flow.append(node.get());
		node.setLabel(opt.label);
		// receiver
		if(node.receivable){
			var receiver = new Receiver();
			node.appendWrapper(receiver);
		}
		// sender
		var i;
		for(i=0; i<node.senderNum; i++){
			var sender = new Sender();
			node.appendWrapper(sender);
			sender.get().css({
				left: (node.get().width()*(i+1)/(node.senderNum+1)-sender.get().width()/2)+'px',
			});
		}
		
		return node;
	},
	// append element
	append: function(obj){
		if(obj instanceof jQuery)
			prop.flow.append(obj);
	},
	
	//xflowResize
	xflowResize:function(){
			var nodeC = $('#xflow').children().length;
			$('#xflow').css('height',nodeC *100 + 200 + 'px');
	},
	// -------------------------------------------
/* struction of data
{id: {offset: {}, receivable: true, sender: [id, id, id]}}
*/
	// output
	exportData: function(){
		var obj = {};
		// enviroments
		obj.env = prop.env;
		// data of nodes
		obj.data = {};
		
		// process for all nodes
		var id = 0;
		prop.flow.children('.node')
			// set id to all nodes
			.each(function(){
				var node = $(this).data('wrapper');
				node.id = id++;
			})
			// get property of the node
			.each(function(){
				var node = $(this).data('wrapper');
				obj.data[node.id] = {
					label: node.label,
					offset: node.get().offset(),
					receivable: node.receivable,
					sender: []
				};
				// get id of node be connected by sender
				node.get().children('.sender')
					.each(function(i){
						var sender = $(this).data('wrapper');
						if(sender.link === null){
							obj.data[node.id].sender[i] = null;
							return true;
						}
						obj.data[node.id].sender[i]
							= sender.link.receiver.get()
								.parent().data('wrapper').id;
					});
			});
		// convert json
		var json = JSON.stringify(obj);
		return json;
	},
	// input
	importData: function(json){
		// check parsability
		var obj = $.parseJSON(json);
		if(obj == null) return false;
		// check compatibility
		if(obj.env.ver !== prop.env.ver) return false;
		// initialize
		Xflow.init();
		// put nodes
		var nodes = [];
		$.each(obj.data, function(key, o){
			var node = Xflow.createNode({
				label: o.label,
				receivable: o.receivable,
				senderNum: o.sender.length,
			});
			node.get().offset(o.offset);
			nodes[key] = node;
		});
		// connect nodes
		$.each(nodes, function(key, o){
				var node = o;
				node.get().children('.sender')
					.each(function(i){
						if(obj.data[key].sender[i] == null) return true;
						var sender = $(this).data('wrapper');
						var link = new Link(sender, nodes[obj.data[key].sender[i]].get().children('.receiver').data('wrapper'));
					});
			});
		// success
		return true;
	},
};
//////////////////////////////////////////////////
// object Wrapper
var Wrapper = function(){
	this.obj = this.create.apply(this, arguments);
	this.obj.data('wrapper', this);
};
$.extend(Wrapper.prototype, {
	obj: null,
	id: 0,
	create: function(){
		return $(document.createElement('div'));
	},
	get: function(){
		return this.obj;
	},
	appendWrapper: function(wrapper){
		this.obj.append(wrapper.obj);
	}
});
//////////////////////////////////////////////////
var Node = function(receivable, senderNum){
	// prop
	this.label = '';
	this.receivable = true;
	this.senderNum = 1;
	Wrapper.apply(this, arguments);
};
$.extend(Node.prototype, Wrapper.prototype, {
	// create
	create: function(option){
		this.receivable = option.receivable;
		this.senderNum = option.senderNum;
		//
		var node =  $(document.createElement('div'))
			.addClass('node')
			.draggable(this.draggable);
		if(this.receivable) node.droppable(this.droppable);
		return node;
	},
	// label
	setLabel: function(string){
		this.label = string;
		var label = $(document.createElement('div')).html(this.label);
		this.obj.append(label);
		label.css({
			position: 'absolute',
			display: 'inline'
		});
		label.css({
			left:(this.obj.width() /2 - label.width() /2)+'px',
			top: (this.obj.height()/2 - label.height()/2)+'px',
		});
	},
	// update link
	update: function(){
		this.obj.children('.sender,.receiver')
			.each(function(){
				$(this).data('wrapper').update()
			});
	},
	// reset pos
	resetPosition: function(){
		var flow = prop.flow;
		var node = this.obj;
		var ofsF = flow.offset();
		var ofsN = node.offset();
		
		if(ofsN.top  < ofsF.top ) ofsN.top  = ofsF.top;
		if(ofsN.left < ofsF.left) ofsN.left = ofsF.left;
		if((ofsN.top  + node.height())  > (ofsF.top  + flow.height()))
			ofsN.top = ofsF.top + flow.height() - node.height();
		if((ofsN.left + node.width() )  > (ofsF.left + flow.width()))
			ofsN.left = ofsF.left + flow.width() - node.width();
		
		node.offset({top: ofsN.top, left: ofsN.left});
		node.data('wrapper').update();
	},
	// remove node
	destroy: function(){
		this.obj.children('.sender').each(function(){
			var sender = $(this).data('wrapper');
			if(sender.link !== null) sender.link.destroy();
		});
		if(this.receivable){
			var receiver = this.obj.children('.receiver').data('wrapper');
			var max = receiver.link.length;
			for(var i=0, d=0; i<max; i++){
				if(receiver.link[i-d] !== null){
					var result = (receiver.obj[0] == receiver.link[i-d].receiver.obj[0]);
					receiver.link[i-d].destroy();
					d++;
				}
			}
		}
		this.obj.remove();
		prop.flow.children('.node')
			.each(function(){
				$(this).data('wrapper').resetPosition();
			});
		Xflow.xflowResize();
	},
	// draggable node
	draggable: {
		drag: function(event, ui){
			$(event.target).data('wrapper').update();
		},
		stop: function(event, ui){
			var node = ui.helper.data('wrapper');
			if(node != null) node.resetPosition();
		}
	},
	//droppable node
	droppable: {
		accept: '.sender',
		drop: function(event, ui){
			event.target = $(event.target).children('.receiver')[0];
			Receiver.prototype.droppable.drop(event, ui);
		},
	},
});
////////////////////
var Sender = function(){
	// prop
	this.link = null;
	Wrapper.apply(this, arguments);
};
$.extend(Sender.prototype, Wrapper.prototype, {
	// create
	create: function(){
		this.link = null;
		return $(document.createElement('div'))
			.addClass('sender')
			.draggable(this.draggable);
	},
	// update
	update: function(){
		if(this.link == null) return;
		this.link.update();
	},
	// link methods (must be called Link object)
	connect: function(link){
		this.link = link;
	},
	disconnect: function(link){
		this.link = null;
	},
	// draggable sender
	draggable: {
		helper: function(event, ui){
			var helper = new Sender();
			return helper.get()
				.addClass('helper')
				.unbind()
				.data('helper', true);
		},
		start: function(event, ui){
			var sender = $(event.target).data('wrapper');
			if(sender.link != null) sender.link.destroy();
			
			var helper = ui.helper.data('wrapper');
			var link = new Link(sender, helper);
		},
		drag: function(event, ui){
			var link = $(event.target).data('wrapper').link;
			link.update();
		},
		stop: function(event, ui){
			var link = $(event.target).data('wrapper').link;
			if(link.receiver.get().data('helper') === true) link.destroy();
		},
	},
});
////////////////////
var Receiver = function(){
	// prop
	this.link = [];
	Wrapper.apply(this, arguments);
};
$.extend(Receiver.prototype, Wrapper.prototype, {
	// create
	create: function(){
		this.link = [];
		return 	this.receiver = $(document.createElement('div'))
			.addClass('receiver')
			.droppable(this.droppable);
	},
	// update
	update: function(){
		if(this.link.length == []) return;
		$.each(this.link, function(i, value){
			value.update();
		});
	},
	// link methods (must be called Link object)
	connect: function(link){
		this.link.push(link);
	},
	disconnect: function(link){
		var array = this.link;
		$.each(array, function(i, value){
			if(value.obj[0] == link.obj[0]){
				array.splice(i, 1);
				return false;
			}
		});
	},
	// droppable receiver
	droppable: {
		accept: '.sender',
		drop: function(event, ui){
			var sender = ui.draggable.data('wrapper');
			if(sender.link != null) sender.link.destroy();
			
			var receiver = $(event.target).data('wrapper');
			var link = new Link(sender, receiver);
		},
	},
});
////////////////////
/*
define a link 'A to B'
A,B refered this object
call update() to update a link
call destroy() to remove a link
*/
var Link = function(){
	// prop
	this.sender = null,
	this.receiver = null,
	Wrapper.apply(this, arguments);
	// init
	prop.flow.append(this.obj);
	this.update();
};
$.extend(Link.prototype, Wrapper.prototype, {
	// create
	create: function(sender, receiver){
		this.sender = sender;
		this.receiver = receiver;
		//
		this.sender.connect(this);
		this.receiver.connect(this);
		//
		return $(document.createElement('div'))
			.addClass('link');
	},
	// update link
	update: function(){
		var posF = prop.flow.offset();
		var posS = this.sender.get().offset();
		var posR = this.receiver.get().offset();
		posS = {
			top : (posS.top  + this.sender.get().height()/2),
			left: (posS.left + this.sender.get().width() /2)
		};
		posR = {
			top : (posR.top  + this.receiver.get().height()/2),
			left: (posR.left + this.receiver.get().width() /2)
		};
		var diffX = posR.left - posS.left;
		var diffY = posR.top  - posS.top;
		var len = Math.sqrt(Math.pow(diffX, 2) + Math.pow(diffY, 2));
		var rad = Math.atan2(diffY, diffX);
		
		this.obj.css({
			top : (posS.top  - posF.top - $('.sender').height()/2) +'px',
			left: (posS.left - posF.left) +'px',
			width: len + 'px',
			transform: 'rotate(' + rad + 'rad)',
		});
	},
	// remove link
	destroy: function(){
		this.sender.disconnect(this);
		this.receiver.disconnect(this);
		this.obj.remove();
		//this.obj = null;
		//this.sender = null;
		//this.receiver = null;
	},
});

})(jQuery);
