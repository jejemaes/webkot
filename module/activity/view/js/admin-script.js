
function activityGetStat(newUrl, actid){
	
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {id : actid},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
			}catch(e){
				res = data;
			}
			
			if(res.message){
				$("#publishing-progress").html(res);
			}else{	
				valuerename = 0;
				valuecopy = 0;
				valuetotal = 1;

				valuerename = res.rename;
				valuecopy = res.copy;
				valuetotal = res.total;
			
				var prename = Math.floor((valuerename / valuetotal) * 100);
				var pcopy = Math.floor((valuecopy / valuetotal) * 100);
			
			
				$("#publishing-pvalue-rename").html(prename + "% ("+valuerename+"/"+valuetotal+")");
				$("#publishing-pvalue-copy").html(pcopy + "% ("+valuecopy+"/"+valuetotal+")");
			
				$("#modal-publishing-rename-progressbar").attr("style","width: "+prename+"%;");
				$("#modal-publishing-rename-progressbar").attr("aria-valuenow",prename);
				$("#modal-publishing-copy-progressbar").attr("style", "width: "+pcopy+"%;");
				$("#modal-publishing-copy-progressbar").attr("aria-valuenow",pcopy);
			
				// colorize the progress bar if finished
				if(prename >= 100){
					$("#modal-publishing-rename-progressbar").attr("class","progress-bar progress-bar-success");
				}
				if(pcopy >= 100){
					$("#modal-publishing-copy-progressbar").attr("class","progress-bar progress-bar-success");
				}
			
				// if SendMail was activated
				if(res.totalmail){
					var pmail = Math.floor((res.currentmail / res.totalmail) * 100);
			
					$("#publishing-pvalue-mailer").html(pmail + "% ("+res.currentmail+"/"+res.totalmail+")");
					$("#modal-publishing-mailer-progressbar").attr("style","width: "+pmail+"%;");
					$("#modal-publishing-mailer-progressbar").attr("aria-valuenow",pmail);
					
					if(pmail >= 100){
						$("#modal-publishing-mailer-progressbar").parent("div").attr("class","progress progress-striped progress-success");
					}
				}else{						
					$("#modal-publishing-mailer").remove();
				}
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#activity-message').html("opps: " + textStatus + " : " + errorThrown);
			$('#activity-message').append("Une erreur est survenue cote server. L'action n'a donc pas ete terminee.");
			return "Error";
		}
	});
}





function activityUnpublish(newUrl, actid){	
	if(confirm("Etes-vous certain de vouloir depublier cette activite ?")){
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'json',
			type: "GET",
			data: {id : actid},
			beforeSend: function () {
				$('#activity-loader-'+actid).css("visibility","visible"); 
			},
			cache: false,
			success: function (data, textStatus, xhr) {
				$('#activity-message').html(SystemUtils.htmlMessage(data.message));
				$('#activity-loader-'+actid).css("visibility","hidden"); 
				var str = $('#activity-action-publish-'+actid).attr("href"); 
				str = str.replace("unpublish","publish");
				$('#activity-action-publish-'+actid).attr("href",str); 
				$('#activity-action-publish-'+actid).attr("onclick",""); 
				$('#activity-action-publish-'+actid).html("<i class=\"icon-leaf\"></i> Publier");
				$('#activity-statut-'+actid).html('<span class="label label-danger">Non</span>');
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#activity-message').html("opps: " + textStatus + " : " + errorThrown);
				$('#activity-loader-'+actid).css("visibility","hidden"); 
			}
		});
	}
}




function activityGetCsv(newUrl){
	var saisie = prompt("Saisissez le nombre d\'activites que vous voulez : ", "un nombre")	;
	if(saisie){		
		window.open(newUrl + "&nbr="+saisie);
	}
}