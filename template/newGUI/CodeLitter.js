;var CodeLitter;
(function($){

// inherit function
function inherit$ (constructor, prop) { return inherit ($.fn.init, constructor, prop); }
function inherit (parent, constructor, prop) {
    var child = function () { constructor.apply(this, [parent.bind(this)].concat(Array.prototype.slice.call(arguments))); };
    $.extend(child.prototype, parent.prototype, prop);
    return child;
}

// cast method
$.fn.asCL = function (type) {
    switch (type) {
    case 'List': case 'list':
        return new List(this);
    case 'Item': case 'item':
        return new Item(this);
    default:
        return new CodeLitter(this);
    }
    return this;
};

// base object
function inheritBase (prop) {
    return inherit$ (
        function (base, selector, context) {
            if (selector === undefined)
                base(this.initialize());
            else
                base(selector, context);
        },$.extend({
            initialize: function(){
                return $();
            },
        }, prop)
    );
}

// util
CodeLitter = function (selector, context) { return new _CodeLitter(selector, context); };


// root container
var _CodeLitter = inheritBase ({
    initialize: function () {
        var obj =  $(document.createElement('div'))
            .addClass('codelitter');
        obj.append(new List().visible(true).addClass('root'));
        return obj;
    },
    //
    getRoot: function () {
        return this.children('.list').asCL('list');
    },
    // base64
    Base64: function (base64) {
        if (base64 === undefined) {
            return btoa(escape(this.JSON()));
        } else {
            return this.JSON(unescape(atob(base64)));
        }
    },
    // json I/O
    JSON: function (json){
        if (json === undefined) {
            return JSON.stringify(this._exportData(this.getRoot()));
        } else {
            var data = {};
            try { data = $.parseJSON(json); } catch (e) { return; }
            this.getRoot().children().remove();
            this._importData(this.getRoot(), data);
        }
    },
    _exportData: function (list) {
        var self = this;
        var items = list.children('.item');
        var result = [];
        items.each(function(){
            var item = $(this);
            var res = {};
            res.code = item.children('.code').html();
	    var selected = item.children('.code').children('select');
	    selected.each(function(){
//		res.data.val.push($(this).val());
	    });
	    var texts = item.children('.code').children('input');
	    texts.each(function(){
		
	    });
            if (item.children('.list').length > 0)
                res.internal = self._exportData(item.children('.list'));
            result.push(res);
        });
        return result;
    },
    _importData: function (list, data) {
        var self = this;
        $.each(data, function(i, val){
            var item = list.addItem(create({name:val.code,value:val.value}));
            if (val.internal !== undefined)
                self._importData(item.addList(), val.internal);
        });
    },
});
CodeLitter.prototype = _CodeLitter.prototype;
// list
var List = inheritBase ({
    initialize: function () {
        var obj = $(document.createElement('ul'))
            .addClass('list')
            .sortable({
                connectWith: '.list',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                update: function (event, ui) {
                    var code = ui.item.asCL('code');
                    if (code.children('.list').length > 0) {
                        var list = code.children('.list').asCL('list');
                        if (list.visible()) list.visible(true);
                    }
                }
            });
        obj.asCL('list')
            .visible(false);
        return obj;
    },
    //
    addItem: function (code) {
        var item = new Item();
        item.code(code);
        return item.appendTo(this).asCL('item');
    },
    visible: function (state) {
        if (state === undefined)
            return (this.css('display').search('none') < 0);
        else
            if (state) {
                this.parent().parent().children('.item').children('.list').each(function(){
                    $(this).asCL('list').visible(false);
                });
            }
            if (this.data('_visibleCallback') != null) this.data('_visibleCallback')(state);
            this.css('display', (state)? 'inline-block': 'none');
        return this;
    },
    
    // private
    _visibleHook: function (callback) {
        this.data('_visibleCallback', callback);
        return this;
    },
});
// list item
var Item = inheritBase({
    initialize: function () {
        var obj = $(document.createElement('li'))
            .addClass('item')
            .append(new Code());
        return obj;
    },
    //
    addList: function () {
        var any = this.children('.list');
        if (any.length > 0)
            return any.adCL('list');
        else
            var list = new List();
            var toggle = new Toggle()._bindList(list);
            this.append(toggle).append(list);
            list.css('left', '100%');
            return list;
    },
    code: function (value) {
        if (value === undefined)
            return this.children('.code').html();
        else
            return this.children('.code').html(value);
    },
});

var Code = inheritBase ({
    initialize: function () {
        var code = $(document.createElement('span'))
            .addClass('code');
        return code;
    },

});

var Toggle = inheritBase ({
    initialize: function () {
        var toggle = $(document.createElement('span'))
            .addClass('toggle');
        return toggle;
    },
    //
    _bindList: function (list) {
        this.click(function () {
            list.visible(!list.visible());
        });
        var self = this;
        list._visibleHook(function(state){ self.html((state)? '－': '＋'); }).visible(false);
        return this;
    },
});

})(jQuery);
