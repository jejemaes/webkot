/**
 * this function take a link (for a web page) and return the page in a modal. It built the modal bootstrap if it doesn't exist yet.
 * @param link : the link to transform in a server action to make the call (from index.php to server.php)
 * @returns {Boolean}
 * @uses Uri class
 */
function activityOverlayPage(baseUrl, link, title){
	var uri = new Uri(link);
	
	var varp = uri.getQueryParamValues('p');
	var serveruri = new Uri(baseUrl);
	
	console.log("begin of the uri transformation");
	serveruri.addQueryParam('action', uri.getQueryParamValues('p'));
	
	
	if(varp == "picture"){
		serveruri.addQueryParam('id',uri.getQueryParamValues('id'));
	}
	if(varp == "mypicture"){
		serveruri.addQueryParam('id',uri.getQueryParamValues('id'));
	}
	if(varp == "lastcomm"){
		serveruri.addQueryParam('index',uri.getQueryParamValues('index'));
	}
	if(varp == "censures"){
		serveruri.addQueryParam('index',uri.getQueryParamValues('index'));
	}
	if(varp == "top10"){
		serveruri.addQueryParam('index',uri.getQueryParamValues('index'));
		serveruri.addQueryParam('type',uri.getQueryParamValues('type'));
		serveruri.addQueryParam('year',uri.getQueryParamValues('year'));
	}
	console.log("end of transformation uri : from index.php to server.php");

	// check if the modal exist
	if (($("#activity-picture-modal").length == 0)){
		 // create the modal
		/*console.log("create of the modal");
		modal = document.createElement("div");
		document.body.appendChild(modal);
		modal.attr('class','modal hide fade');
		console.log("createion progresseionion");
		modal.attr('id','activity-picture-modal');
		return false;
		console.log(modal);
		modal.prop("id","activity-picture-modal");
		modal.attr('tabindex','-1').attr('data-width','950').attr('class','modal hide fade');
		var header = document.createElement("div");
		header.html('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3 id="activity-picture-modal-title">'+title+'</h3>');
		var body = document.createElement("div");
		body.attr('id','activity-picture-modal-content')
			.attr('class','modal-body');
		body.html('<div class="row-fluid">LOADING ...</div>');*/
		//modal.append(header).append(body);
		//console.log("body-->"+body);
		
		var modal = '<div class="modal fade" id="activity-picture-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
			modal += '<div class="modal-dialog modal-lg">';
			modal += '<div class="modal-content">';
			modal += '<div class="modal-header">';
			modal += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			modal += '<h4 class="modal-title" id="activity-picture-modal-title">'+title+'</h4>';
			modal += '</div>';
			modal += '<div class="modal-body" id="activity-picture-modal-content">';
			modal += '...';
			modal += '</div>';
			/*modal += '<div class="modal-footer">';
			modal += '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
			modal += '<button type="button" class="btn btn-primary">Save changes</button>';
			modal += '</div>';*/
			modal += '</div>';
			modal += '</div>';
			modal += '</div>';
		
		
		$("body").append(modal);
	}
	
	// make the modal visible (if not already)
	console.log("before the check if the modal is visible");
	if(!($("#activity-picture-modal").is(":visible"))){	
		console.log("the modal is not visible");
		try{
			
			$("#activity-picture-modal").modal('show');
		}catch(e){
			console.log(e);
		}
		console.log("the modal is now visible");
	}
	//set the content of the modal
	console.log("update the content of the modal");
	$.ajax({
		url: serveruri.toString(),
		async: true,
		dataType: 'html',
		type: "GET",
		data: {},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
				if(res.message){	
					$('#activity-picture-modal-content').html(displayMessage(res.message));
				}else{
					$('#activity-picture-modal-content').html(JSON.stringify(res));
				}
			}catch(e){
				$("#activity-picture-modal-content").html(data);
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#activity-picture-modal-content').html("ERROR<br>opps: " + textStatus + " : " + errorThrown);
		}
	});
	return false;
}

function displayMessage(message){
	if(message.type === 'error'){
		return '<div class="alert alert-error alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message.content+'</div>';
	}else{
		if(message.type === 'success'){
			return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message.content+'</div>';
		}else{
			return '<div class="alert alert-warn alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message.content+'</div>';
		}
	}
	return message;
}

function activityAskCensure(pid, mail, comment, newUrl){
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'json',
		type: "POST",
		data: {pid : pid, email : mail, comment : comment},
		cache: false,
		success: function (data, textStatus, xhr) {
			try{
				res = JSON.parse(data);
			}catch(e){
				res = data;
			}
			if(res.message){	
				$('#activity-modal-message').html(displayMessage(res.message));
			}else{
				$('#activity-modal-message').html(JSON.stringify(res));
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#activity-modal-message').html("ERROR<br>opps: " + textStatus + " : " + errorThrown);
		}
	});
	$('#activity-censure-modal').modal('hide');
}

function activityDisplayCommentList(list, actions){
	var html = '<div class="activity-modal-infos">';
	for (var i=0;i<list.length;i++){
		html += activityDisplayComment(list[i], actions);
	}
	html += '</div>';
	return html;
}

/**
* generate the html code for a JSON comment
* @param comment
* @returns
*/
function activityDisplayComment(comment, actions){	
	var html = '<div id="activity-comment-'+ comment.id +'" >';
	html += '<b><a href="' + SystemUtils.urlUser(comment.userid) + '">'+comment.userid+'</a></b>, le <i>' + comment.date + '</i>';
//console.log("avant ACTION");
	html += activityDisplayCommentAction(comment, actions);
//console.log("apres ACTION");
	html += '<br/>';
//console.log("avant encoding");
	html += Encoder.htmlDecode(comment.comment);
//console.log("apres encoding");
	html += '<hr class="activity-hr-style">';
	html += '</div>';
	return html;
}

function activityDisplayCommentAction(comment, actions){
	var html = '';
	for (var i=0;i<actions.length;i++){
		var act = actions[i];
		var href = act.href;
		var params = act.param;
		for (p in params){
			var thefield = params[p];
			//href = href.replace(p,comment[thefield]);
			href = activityReplaceAll(p, comment[thefield], href);
		}
		html += ' - <a href="'+href+'" class="btn btn-danger btn-xs">'+act.title+'</a> ';
		
		//var thefield = tmp.field; 00h22 ca marchait
		//html += '<a href="javascript:activityDeleteComment('+tmp.url+comment[thefield]+','+comment.id+')" class="btn btn-danger btn-mini">'+tmp.name+'</a>';
	}
	return html;
}





function activityDeleteComment(newUrl,cid){
	
	if(confirm("Etes-vous certain de vouloir supprimer ce commentaire ?")){
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'html',
			type: "POST",
			data: {id : cid},
			cache: false,
			success: function (data, textStatus, xhr) {
				res = JSON.parse(data);
				if(res.message){	
					$('#activity-modal-message').html(displayMessage(res.message));
				}
				if(res.message.type = "success"){
					var tagname = 'activity-comment-' + cid;
					$('#'+tagname).remove();
				}
				myRtnA = "Success"
					return myRtnA;
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#activity-modal-message').html("opps: " + textStatus + " : " + errorThrown);
				$('#activity-modal-loading-comment').html('');
				myRtnA = "Error"
					return myRtnA;
			}
		});
	}
	
}



function activitySendComment(newUrl, pictId, userId){
	
	var text = $('#activity-comment-textarea').val();
	if(text){
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'html',
			beforeSend: function () {
				$('#activity-modal-loading-comment').removeClass( "activity-invisible" ).addClass( "activity-visible" );
			},
			type: "POST",
			data: {pid : pictId, uid : userId, comment : text},
			cache: false,
			success: function (data, textStatus, xhr) {
				res = JSON.parse(data);
				$('#activity-modal-loading-comment').removeClass( "activity-visible" ).addClass( "activity-invisible" );
				if(res.message){	
					$('#activity-modal-message').html(displayMessage(res.message));
				}
				if(res.comments){
					lecode = activityDisplayCommentList(res.comments, res.actions);
					$('#activity-modal-comments').html("<h3>Commentaires</h3>" + lecode);
					$('#test').html(lecode);
					$('#activity-comment-textarea').val('');
					$('#activity-the-picture').addClass("activity-img-commented");
				}
				return "Success";
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#activity-modal-message').html("opps: " + textStatus + " : " + errorThrown);
				$('#activity-modal-loading-comment').html('');
				return "Error";
			}
		});
	}else{
		alert("Le commentaire que vous soumettez est vide !");
	}
}


function activityChangeCensure(newUrl, pictId, censureVal){

	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'html',
		beforeSend: function () {
			
		},
		type: "POST",
		data: {id : pictId, value : censureVal},
		cache: false,
		success: function (data, textStatus, xhr) {
			res = JSON.parse(data);
			if(res.message){
				$('#activity-modal-message').html(displayMessage(res.message));
				if(res.message.type = "success"){					
					if(censureVal){
						$('#activity-the-picture').addClass("activity-img-censured").removeClass("img-polaroid");
						address = 'javascript:activityChangeCensure(\''+newUrl+'\','+pictId+',0);';
						label = "Decensurer";
					}else{
						$('#activity-the-picture').addClass("img-polaroid").removeClass("activity-img-censured");
						address = 'javascript:activityChangeCensure(\''+newUrl+'\','+pictId+',1);';
						label = "Censurer";
					}
					$('#activity-action-censure').attr( "href", address );
					$('#activity-action-censure').text(label);	
				}
			}
			return "Success";
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#activity-modal-message').html("opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
}


function activityRotationPicture(newUrl, pictId, degreeVal){

	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'html',
		beforeSend: function () {
			
		},
		type: "POST",
		data: {id : pictId, degree : degreeVal},
		cache: false,
		success: function (data, textStatus, xhr) {
			res = JSON.parse(data);
			if(res.message){	
				$('#activity-modal-message').html(displayMessage(res.message));
				srcVal = $('#activity-the-picture').attr("src");
				d = new Date();
				$('#activity-the-picture').attr("src", srcVal+"?"+d.getTime());
			}
			return "Success";
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#activity-modal-message').html("opps: " + textStatus + " : " + errorThrown);
			return "Error";
		}
	});
	
}


function activityAddFavorite(newUrl,pid){
	$.ajax({
		url: newUrl,
		async: true,
		dataType: 'html',
		type: "POST",
		data: {id : pid},
		cache: false,
		success: function (data, textStatus, xhr) {
			res = JSON.parse(data);
			if(res.message){	
				$('#activity-modal-message').html(displayMessage(res.message));
			}
			myRtnA = "Success"
			return myRtnA;
		},
		error: function (xhr, textStatus, errorThrown) {
			alert(xhr);
			$('#activity-modal-message').html("opps: " + textStatus + " : " + errorThrown);
			$('#activity-modal-loading-comment').html('');
			myRtnA = "Error"
				return myRtnA;
		}
	});
}



function activityDeleteFavorite(newUrl,pid){
	if (confirm("Etes-vous certain de vouloir supprimer cette photo de vos favoris ?")) {	
		$.ajax({
			url: newUrl,
			async: true,
			dataType: 'html',
			type: "POST",
			data: {id : pid},
			cache: false,
			success: function (data, textStatus, xhr) {
				res = JSON.parse(data);
				if(res.message){	
					$('#activity-mypicture-message').html(displayMessage(res.message));
				}
				if(res.message.type = "success"){				
					$('#activity-mypicture-'+pid).remove();
				}
				myRtnA = "Success"
					return myRtnA;
			},
			error: function (xhr, textStatus, errorThrown) {
				alert(xhr);
				$('#activity-mypicture-message').html("opps: " + textStatus + " : " + errorThrown);
				myRtnA = "Error"
					return myRtnA;
			}
		});
	}
}


function activityShowModal(id){
	$('#'+id).modal('show');
}

function activityMakeModalNonState(url, module, action, pid){				
	$.get(url+"server.php?module="+module+"&action="+action+"&id="+pid,
			function(data) {	
				$("#activity-picture-modal").modal({
					show: true
				});
				$("#activity-picture-modal-content").html(data);
			}
	);
}



function activityReplaceAll(find, replace, str) {
	  return str.replace(new RegExp(find, 'g'), replace);
}


