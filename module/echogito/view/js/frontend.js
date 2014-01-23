

function echogitoHtmlTableEchogito(echogito){

	var HTML = '<ul class="nav nav-tabs" id="echogito-tabpanel">';
	for (var key in echogito){
		HTML += '<li><a href="#'+key+'" data-toggle="tab">'+key+'</a></li>';
	}
	HTML += '</ul>';
	
	HTML += '<div class="tab-content" style="min-height:400px;">';
	for(var key in echogito){
		var events = echogito[key];
		HTML += '<div class="tab-pane" id="'+key+'">';
		HTML += '<div class="row">';
			//HTML += JSON.stringify(events); 
			if(events.length > 0){
				for (var i=0;i<events.length;i++){
					event = events[i];
					HTML += echogitoHtmlMediaEvent(event);
					if(i % 2 != 0){
						HTML += '</div><hr><div class="row">';
					}
				}
			}else{
				HTML += '<div class="col-lg12 col-md-12 col-sm-12 col-xs-12">Il n\'y a pas d\'&eacute;v&egrave;venement ce jour-la. On va donc devoir "&eacute;tudier" ...</div>';
			}
			HTML += '</div>';
		HTML += '</div>';
	}
	HTML += '</div>';
	return HTML;
}

/**
 * get the name of the month in french, giving the index (number of month)
 * @param num : the number of the month (1 for January, ..., 12 for December)
 * @returns the name of the month
 */
function echogitoGetMonthName(num){
	num--;
	var months = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];
	return months[num];
}

/**
 * get the name of the day in french, giving the index (number of day)
 * @param num : the number of the month (1 for Monday, ..., 7 for Sunday)
 * @returns the name of the day
 */
function echogitoGetDayName(num){
	if(num == 0){
		return "Dimanche";
	}else{		
		num--;
		var days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
		return days[num];
	}
}

/**
 * transform a datetime sql into a human readable string
 * @param datetime
 * @returns {String}
 */
function echogitoDatetimeReadable(datetime){
	var tmp = datetime.split(" ");
	date = tmp[0].split("-");
	time = tmp[1].split(":");
	return "Le "+date[2]+"/"+date[1]+"/"+date[0]+" &agrave; "+ time[0] +"h"+time[1];
}


/**
 * built the html code of a given event to put in the Calendar (media style)
 * @param string $modname : the name of the module
 * @param Event $event : the Event to display
 * @param string $class : the css class the higher div must have
 * @return string : the html code
 */
function echogitoHtmlMediaEvent(event){
	var html = '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
	html += '<p class="lead">'+event.name+'</p>';
	html += '<p >'+event.description.substring(0,500)+' ...</p>';
	// URL '.URLUtils::generateURL($modname, array("p"=>"event", "id" => $event->getId())).'
	url = document.URL + "&p=event&id="+event.id;
	html += '<br><a class="btn btn-primary btn-sm pull-right" href="'+url+'">&#187; Lire plus</a>';
	html += '<ul class="list-unstyled">';
	html += '<li><i class="fa fa-user"></i> <strong>Organis&eacute; par </strong> '+event.organizer+'</li>';
	html += '<li><i class="fa fa-clock-o"></i> <strong>Heure :</strong> '+echogitoDatetimeReadable(event.start_time)+'</li>';
	html += '<li><i class="fa fa-globe"></i> <strong>Lieu :</strong> '+event.location+'</li>';
	if(event.facebookid){
		html += '<li><i class="fa fa-facebook-square"></i> <a href="https://www.facebook.com/events/'+event.facebookid+'" target="_blank">Lien Facebook</a></li>';
	}
	if(event.categoryid){
		html += '<li><span style="color:'+event.categorycolor+'"><i class="fa fa-folder-open"></i> '+event.categoryname+'</span></li>';
	}
	html += '</ul>';
	html += '<div class="clearfix"></div>';
	html += '</div>';
	return html;
}


/**
 * 
 */
function echogitoHtmlListEvent(events){
	var html = "";
	var i=0;
	var currentDateMonth = "";
	while(i < events.length){
		event = events[i];
		datevar = event.start_time.split("-");
		eventDateMonth = datevar[0]+"-"+datevar[1];
		if(eventDateMonth != currentDateMonth){
			currentDateMonth = eventDateMonth;
			html += "<h4>"+echogitoGetMonthName(datevar[1])+" "+datevar[0]+"</h4>";
			html += '<hr>';
		}
		html += '<div class="row">';
	//	html += '<div class="col-lg-4"><span class="text-muted">'+SystemUtils.datetimeReadable(event.start_time)+'</span></div>';
		html += '<div class="col-lg-4"><span class="text-muted">'+echogitoDatetimeReadable(event.start_time)+'</span></div>';
		html += '<div class="col-lg-7"><strong>'+event.name+'</strong>';
		html += ', organis&eacute; par '+event.organizer;
		if(event.categoryid){
			html += '<br><span style="color:'+event.categorycolor+'"><i class="fa fa-folder-open"></i> '+event.categoryname+'</span>';
		}
		html += '</div>';
		html += '<div class="col-lg-1"><a class="btn btn-primary btn-sm" href="'+event.event_url+'">&#187; Lire plus</a></div>';
		html += '</div><hr>';
		
		i++;
	}
	return html;
}

/**
 * fetch the list of event (echogito), order by day of the week
 * @param serverUrl : the url to get the request
 * @returns events : an key array (key = the day name and the value is an array of event)
 */
function echogitoFetchEchogito(serverUrl){
	$.ajax({
		url: serverUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {},
		cache: false,
		beforeSend: function () {
			$( "#echogito-the-echogito" ).html('<span style="text-align: center;"><i class="fa fa-spinner fa-5x fa-spin"></i>LOADING<span>');
		},
		success: function (data, textStatus, xhr) {
			var code = echogitoHtmlTableEchogito(data);
			$( "#echogito-the-echogito" ).html(code);
			index = new Date().getDay();
			day = echogitoGetDayName(index);
			$( '#echogito-tabpanel a[href="#'+day+'"]').tab('show');
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#echogito-message').html("ERROR<br>opps: " + textStatus + " : " + errorThrown);
			//echogito = {"Monday":[],"Tuesday":[],"Wednesday":[],"Thursday":[],"Friday":[],"Saterday":[],"Sunday":[]};
			echogito = {"Lundi":[],"Mardi":[],"Mercredi":[],"Jeudi":[],"Vendredi":[],"Samedi":[],"Dimanche":[]};
			var code = echogitoHtmlTableEchogito(echogito);
			$( "#echogito-the-echogito" ).html(code);
		}
	});
}



/**
 * fetch the list of event (echogito), order by day of the week
 * @param serverUrl : the url to get the request
 * @returns events : an key array (key = the day name and the value is an array of event)
 */
function echogitoFetchLaterEvents(serverUrl,pagenum){
	$.ajax({
		url: serverUrl,
		async: true,
		dataType: 'json',
		type: "GET",
		data: {"page" : pagenum},
		cache: false,
		beforeSend: function () {
			$( "#echogito-later-content" ).html('<span style="text-align: center;"><i class="fa fa-spinner fa-5x fa-spin"></i>LOADING<span>');
		},
		success: function (data, textStatus, xhr) {
			//var code = echogitoHtmlTableEchogito(data);
			$( "#echogito-later-content" ).html(echogitoHtmlListEvent(data));
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#echogito-message').html("ERROR<br>opps: " + textStatus + " : " + errorThrown);
			$( "#echogito-later-content" ).html("bad error !");
		}
	});
}