
function systemGetLog(newUrl, logname){
	
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'html',
		type: "GET",
		data: {action : "getlog", id : logname},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
				if(res.message){	
					$('#system-log-message').html(SystemUtils.htmlMessage(res.message));
				}else{
					$('#system-log-message').html(JSON.stringify(res));
				}
			}catch(e){
				$('#system-log-content-'+logname).html(data);
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#system-log-message').html("opps: " + textStatus + " : " + errorThrown);
			$('#system-log-message').append("Une erreur est survenue cote server. L'action n'a donc pas ete terminee.");
			return "Error";
		}
	});
}




function systemDeleteLog(newUrl, logname){	
	if(confirm("Etes-vous certain de vouloir supprimer ce log ?")){
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'json',
			type: "GET",
			data: {action : "delete", id : logname},
			cache: false,
			success: function (data, textStatus, xhr) {
				$('#system-log-message').html(JSON.stringify(data));
				try{
					res = JSON.parse(data);
					if(res.message){	
						$('#system-log-message').html(SystemUtils.htmlMessage(res.message));
						alert(res.message);
						alert(res.message.type);
						if(res.message.type == "success"){							
							$('#system-log-content-'+logname).html("");
						}
					}else{
						$('#system-log-message').html(JSON.stringify(res));
					}
				}catch(e){
					if(data.message){	
						$('#system-log-message').html(SystemUtils.htmlMessage(data.message));
						if(data.message.type == "success"){							
							$('#system-log-content-'+logname).html("Log supprim&eacute;");
						}
					}else{
						$('#system-log-message').html(JSON.stringify(data));
					}
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#system-log-message').html("opps: " + textStatus + " : " + errorThrown);
				$('#system-log-message').append("Une erreur est survenue cote server. L'action n'a donc pas ete terminee.");
				return "Error";
			}
		});
	}
}