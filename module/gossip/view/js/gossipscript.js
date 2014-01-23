

function gossipGetPageContent(newUrl, numero){
		var tmp = $.ajax({
			url: newUrl,
			async: false,
			dataType: 'html',
			type: "POST",
			data: {'num' : numero},
			cache: false,
			beforeSend: function(){
				$( "#gossip-page-content" ).html('<span style="text-align: center;"><i class="fa fa-spinner fa-5x fa-spin"></i>LOADING<span>');
			},
			success: function (data, textStatus, xhr) {
				return data;
			},
			error: function (xhr, textStatus, errorThrown) {
				var text = "opps: " + textStatus + " : " + errorThrown;
				return '{"message" : {"type" : "error", "content" : "'+text+'"}}';
			}
		});
		$("html, body").animate({ scrollTop: 10 }, "slow");
		try{
		   var json = JSON.parse(tmp.responseText);
		   if(json.message){
			   return SystemUtils.htmlMessage(json.message);
		   }else{
			   return tmp.responseText;
		   }
		}catch(e){
			//do nothing
		}
		return tmp.responseText;
}


function gossipComment(newUrl, gid, username, action){
	$.ajax({
		url: newUrl+ action,
		async: true,
		dataType: 'json',
		beforeSend: function () {
			
		},
		type: "POST",
		data: {id : gid},
		cache: false,
		success: function (data, textStatus, xhr) {
			
			$('#gossip-message-'+gid).html(SystemUtils.htmlMessage(data.message));
			
			if(data.message.type == "success"){
				if(action == "like" || action == "unlike"){
					var label = "like";
					var n = parseInt($('#gossip-'+label+'r-'+gid).html());
					if(action == "like"){
						n = n+1;
						$('#gossip-like-button-'+gid).html('<i class="fa fa-white fa fa-thumbs-up"></i> Je n\'aime plus').attr("onclick","gossipComment('"+newUrl+"', "+gid+", '"+username+"','unlike')");
					}else{
						n = n-1;
						$('#gossip-like-button-'+gid).html('<i class="fa fa-white fa fa-thumbs-up"></i> J\'aime ').attr("onclick","gossipComment('"+newUrl+"', "+gid+", '"+username+"','like')");
					}
					$('#gossip-'+label+'r-'+gid).html(n);
				}else{
					var label = "dislike";
					var n = parseInt($('#gossip-'+label+'r-'+gid).html());
					if(action == "dislike"){
						n = n+1;
						$('#gossip-dislike-button-'+gid).html('<i class="fa fa-white fa fa-thumbs-down"></i> Je n\'aime pas plus').attr("onclick","gossipComment('"+newUrl+"', "+gid+", '"+username+"','undislike')");
					}else{
						n = n-1;
						$('#gossip-dislike-button-'+gid).html('<i class="fa fa-white fa fa-thumbs-down"></i> Je n\'aime pas').attr("onclick","gossipComment('"+newUrl+"', "+gid+", '"+username+"','dislike')");
					}
					$('#gossip-'+label+'r-'+gid).html(n);
				}
			}
			return "Success";
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#gossip-message').html("opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
}




function gossipGetCommenter(newUrl,gid){
	var tmp = $.ajax({
		url: newUrl,
		async: false,
		dataType: 'json',
		type: "POST",
		data: {'id' : gid},
		cache: false,
		success: function (data, textStatus, xhr) {
			return data;
		},
		error: function (xhr, textStatus, errorThrown) {
			var text = "opps: " + textStatus + " : " + errorThrown;
			m = '{"message" : {"type" : "error", "content" : "'+text+'"}}';
			$('#gossip-message-'+gid).html(SystemUtils.htmlMessage(data.message));
			return "Error";
		}
	});

	try{
		var json = JSON.parse(tmp.responseText);
		if(json.message){
			$('#gossip-message-'+gid).html(SystemUtils.htmlMessage(data.message));
			return "Error";
		}else{
			var html = "";
			for(key in json){
				html += '<a href="'+SystemUtils.urlUser(key)+'">'+json[key]+'</a>, ';
			}
			return html;
		}
	}catch(e){
		//do nothing
		return "error";
	}
}



function gossipCensureAction(newUrl, gid, action){
	var tmp = $.ajax({
		url: newUrl,
		async: false,
		dataType: 'json',
		type: "POST",
		data: {'id' : gid, 'action' : action},
		cache: false,
		success: function (data, textStatus, xhr) {	
			$('#gossip-message-'+gid).html(SystemUtils.htmlMessage(data.message));
			if(data.message.type == "success"){
				if(action == "censure"){
					$('#gossip-censure-button-'+gid).html('<i class="fa fa-circle-o"></i> D&eacute;censure').attr("onclick","gossipCensureAction('"+newUrl+"', "+gid+",'uncensure')");
					$('#gossip-content-'+gid).addClass("text-danger");
				}else{
					$('#gossip-censure-button-'+gid).html('<i class="fa fa-ban"></i> Censure').attr("onclick","gossipCensureAction('"+newUrl+"', "+gid+",'censure')");
					$('#gossip-content-'+gid).removeClass("text-danger");
				}
			}
			return data;
		},
		error: function (xhr, textStatus, errorThrown) {
			var text = "opps: " + textStatus + " : " + errorThrown;
			m = '{"message" : {"type" : "error", "content" : "'+text+'"}}';
			$('#gossip-message-'+gid).html(SystemUtils.htmlMessage(m));
			return "Error";
		}
	});
}


function gossipDeleteAction(newUrl, gid, action){
	 if (confirm("Voulez-vous vraiment supprimer ce potin définitivement ?")) {		 
		 $.ajax({
			 url: newUrl,
			 async: false,
			 dataType: 'json',
			 type: "POST",
			 data: {'id' : gid, 'action' : action},
			 cache: false,
			 success: function (data, textStatus, xhr) {	
				 $('#gossip-message').html(SystemUtils.htmlMessage(data.message));
				 if(data.message.type == "success"){
					 $('#gossip-'+gid).remove();
				 }
				 return data;
			 },
			 error: function (xhr, textStatus, errorThrown) {
				 var text = "opps: " + textStatus + " : " + errorThrown;
				 m = '{"message" : {"type" : "error", "content" : "'+text+'"}}';
				 $('#gossip-message-'+gid).html(SystemUtils.htmlMessage(m));
				 return "Error";
			 }
		 });
	 }
}
