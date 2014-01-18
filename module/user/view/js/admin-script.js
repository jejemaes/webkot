

function userGrantAction(newUrl,userid){
	
	var e = document.getElementById("user-input-role");
	var roleid = e.options[e.selectedIndex].value;
	
	
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {rid : roleid, uid : userid},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
			}catch(e){
				res = data;
			}
			if(res.message){
				$('#user-div-message').html(SystemUtils.htmlMessage(res.message));
				$('#user-label-role-'+userid).html(res.role.role);
			}else{
				$('#user-div-message').html(JSON.stringify(res));
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#user-div-message').html("ERROR <br>opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
	
	$('#user-privilege-modal').modal('hide');
}


function userGrantModalAction(newUrl, userrole, userid, actionUrl){
	
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
			}catch(e){
				res = data;
			}
			if(res.message){
				if(res.message.type = "success"){
					var html = '<select id="user-input-role" name="user-input-role" class="input-xlarge">';
					var index;
					for (index = 0; index < res.roles.length; ++index) {
						var role = res.roles[index];
						if(userrole == role.role){
							html += '<option value="'+role.id+'" selected="selected">';
						}else{							
							html += '<option value="'+role.id+'">';
						}
						html += role.role;
						html += '</option>';
					}
					html += '</select>';
					
					var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
					footer += '<button type="button" class="btn btn-primary" id="user-privilege-modal-save-button3" onclick="userGrantAction(\''+actionUrl+'\',\''+userid+'\');">Save changes</button>'; 
					
					$('#user-privilege-modal div.modal-body').html(html);
					$('#user-privilege-modal div.modal-footer').html(footer);
					//$('#user-privilege-modal-save-button').attr('onclick','alert("sdfsf");');
				}
			}else{
				$('#user-privilege-modal div.modal-body').html(JSON.stringify(res));
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#user-privilege-modal div.modal-body').html("ERROR <br>opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
	
	$('#user-privilege-modal').modal();
}




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
					if(val == 'false'){
						var html = '<a href="#" onclick="return userMailwatchAction(\''+newUrl+'\', '+userid+', \'true\');"><i class="icon-envelope"></i> Abonner</a>';
						$('#user-action-mailwatch-'+userid).html(html);
					}else{
						var html = '<a href="#" onclick="return userMailwatchAction(\''+newUrl+'\', '+userid+', \'false\');"><i class="icon-envelope"></i> D&eacute;sabonner</a>';
						$('#user-action-mailwatch-'+userid).html(html);
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

