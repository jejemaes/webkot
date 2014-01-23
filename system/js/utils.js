

SystemUtils = {

	urlUser : function(userid){
		return 'index.php?mod=user&profile='+userid;
	},
	
	htmlMessage : function (message){
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
	},
	
	parse_url : function(str, component) {
		  // http://kevin.vanzonneveld.net --> http://phpjs.org/functions/parse_url/
		  // +      original by: Steven Levithan (http://blog.stevenlevithan.com)
		  // + reimplemented by: Brett Zamir (http://brett-zamir.me)
		  // + input by: Lorenzo Pisani
		  // + input by: Tony
		  // + improved by: Brett Zamir (http://brett-zamir.me)
		  // %          note: Based on http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
		  // %          note: blog post at http://blog.stevenlevithan.com/archives/parseuri
		  // %          note: demo at http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
		  // %          note: Does not replace invalid characters with '_' as in PHP, nor does it return false with
		  // %          note: a seriously malformed URL.
		  // %          note: Besides function name, is essentially the same as parseUri as well as our allowing
		  // %          note: an extra slash after the scheme/protocol (to allow file:/// as in PHP)
		  // *     example 1: parse_url('http://username:password@hostname/path?arg=value#anchor');
		  // *     returns 1: {scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}
		  var query, key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port',
		            'relative', 'path', 'directory', 'file', 'query', 'fragment'],
		    ini = (this.php_js && this.php_js.ini) || {},
		    mode = (ini['phpjs.parse_url.mode'] &&
		      ini['phpjs.parse_url.mode'].local_value) || 'php',
		    parser = {
		      php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		      strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		      loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
		    };

		  var m = parser[mode].exec(str),
		    uri = {},
		    i = 14;
		  while (i--) {
		    if (m[i]) {
		      uri[key[i]] = m[i];
		    }
		  }

		  if (component) {
		    return uri[component.replace('PHP_URL_', '').toLowerCase()];
		  }
		  if (mode !== 'php') {
		    var name = (ini['phpjs.parse_url.queryKey'] &&
		        ini['phpjs.parse_url.queryKey'].local_value) || 'queryKey';
		    parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
		    uri[name] = {};
		    query = uri[key[12]] || '';
		    query.replace(parser, function ($0, $1, $2) {
		      if ($1) {uri[name][$1] = $2;}
		    });
		  }
		  delete uri.source;
		  return uri;
	},

	datetimeReadable : function(datetime){
		variable = datetime.split(" ");
		date = variable[0].split("-");
		return date[2]+"-"+date[1]+"-"+date[0]+" &agrave; "+ variable[1];
	},
	
	
	fctName : function(){
		//code here
	}
	
	
}