var RECONNECT_TIME = 5000;

function process(){
    $.ajax({
        url: "api",
        type: "GET",
        datatype: "json",
    }).done(function(data){
        $.each(data.templates, function(k, v){
            lwte.addTemplate(k, v);
        });
        $.each(data.datas, function(k, v){
            $(k).html(lwte.useTemplate(v.name, v.data));
        });
        setTimeout(function(){
            process();
        }, data.next);
    }).fail(function(){
        connect_error();
    });
}

function connect_error(){
    setTimeout(function(){
        process();
    }, RECONNECT_TIME);
}
