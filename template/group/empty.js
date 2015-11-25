jQuery( function() { 
	 //HTMLを初期化  
	 jQuery("ul.jquery-ui-sortable li").html("");  
  
    //HTMLを生成  
	jQuery.getJSON("data.json", function(data){  
		jQuery(data.release).each(function(){  
			var i=0;
			i++;
			if(i%3==0){
				 jQuery('<li>'+this.name+
				'<span>' + this.id  + '</span>'+  
				'</li>' ).appendTo('ul.jquery-ui-sortable li');  
			}else if(i%2==0){
				 jQuery('<li>'+this.name+
				'<span>' + this.id  + '</span>'+  
				'</li>' ).appendTo('ul.jquery-ui-sortable li');  
			}else{
				 jQuery('<li>'+this.name+
				'<span>' + this.id  + '</span>'+  
				'</li>' ).appendTo('ul.jquery-ui-sortable li');  
			}
		})
	})
});