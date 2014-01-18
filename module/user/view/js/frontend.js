function userMailwatchAction(newUrl, userid, val){
	
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {id : userid, value : val},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
			}catch(e){
				res = data;
			}
			if(res.message){
				if(res.message.type = "success"){
					console.log(val);
					if(val == 'false'){
						$('#user-label-mailwatch').html('Non');
						var html = '<a class="btn btn-primary" href="#" onclick="return userMailwatchAction(\''+newUrl+'\', '+userid+', \'true\');"><i class="fa fa-envelope"></i> Abonner</a>';
						console.log(html);
						$('#user-action-mailwatch').html(html);
					}else{
						$('#user-label-mailwatch').html('Oui');
						var html = '<a class="btn btn-primary" href="#" onclick="return userMailwatchAction(\''+newUrl+'\', '+userid+', \'false\');"><i class="fa fa-envelope"></i> D&eacute;sabonner</a>';
						console.log(html);
						$('#user-action-mailwatch').html(html);
					}
				}
				$('#user-div-message').html(SystemUtils.htmlMessage(res.message));
			}else{
				$('#user-div-message').html(JSON.stringify(res));
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#user-div-message').html("ERROR <br>opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
	return false;

}