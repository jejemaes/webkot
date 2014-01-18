
function blogSendComment(newUrl, postId, userid){
	var text = $('#blog-input-comment').val();
	if(text){
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'json',
			type: "POST",
			data: {'id' : postId, 'blogcomment' : text},
			beforeSend: function () {
				$('#blog-loading-comment').removeClass( "blog-invisible" ).addClass( "blog-visible" );
			},
			cache: false,
			success: function (data, textStatus, xhr) {
				//alert(JSON.stringify(data));
				if(data.message){	
					$('#blog-message').html(SystemUtils.htmlMessage(data.message));
				}
				if(data.id){
					var ladate = new Date();
					dateComm = ladate.getDate()+"/"+(ladate.getMonth()+1)+"/"+ladate.getFullYear() + " &agrave; "+ladate.getHours()+"h"+ladate.getMinutes();
		
					var html = '<div id="blog-comment-'+data.id+'" class="blog-comment">';
					html += '<b><a href="'+SystemUtils.urlUser(userid)+'">'+userid+'</a></b>';
					html += ', <i>le '+dateComm+'</i>';
					html += '<p>'+ Encoder.htmlDecode(text)+'</p>';
					html += '</div>';
					$('#blog-comments-div').append(html);
				}
				$('#blog-loading-comment').removeClass( "blog-visible" ).addClass( "blog-invisible" );	
				$('#blog-input-comment').val('');
				return "Success";
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#blog-message').html("opps: " + textStatus + " : " + errorThrown);
				$('#blog-loading-comment').removeClass( "blog-visible" ).addClass( "blog-invisible" );
				return "Error";
			}
		});
	}else{
		alert("Le commentaire que vous soumettez est vide !");
	}
}


function test(newUrl, postId, userid){
	var text = $('#blog-input-comment').val();
	alert(userid + " : " + text);
	alert(postId + " test : " + newUrl);
}

function blogDeleteComment(newUrl,cid){
	if (confirm("Etes-vous certain de vouloir supprimer ce commentaire ?")) {	
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'json',
			type: "POST",
			data: {id : cid},
			cache: false,
			success: function (data, textStatus, xhr) {
				if(data.message){	
					$('#blog-message').html(SystemUtils.htmlMessage(data.message));
					$('#blog-comment-'+cid).remove();
				}
				return "Success";
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#blog-message').html("opps: " + textStatus + " : " + errorThrown);
				return "Error";
			}
		});
	}
}
