blackcms.define('web.utils', function(require){

	/**
	 * Create a cookie
	 * @param {String} name : the name of the cookie
	 * @param {String} value : the value stored in the cookie
	 * @param {Integer} ttl : time to live of the cookie in millis. -1 to erase the cookie.
	 */
	function set_cookie(name, value, ttl) {
	    ttl = ttl || 24*60*60*365;
	    document.cookie = [
	        name + '=' + value,
	        'path=/',
	        'max-age=' + ttl,
	        'expires=' + new Date(new Date().getTime() + ttl*1000).toGMTString()
	    ].join(';');
	};
	
	/**
	 * Get a cookie
	 */
	function get_cookie (c_name) {
	    var cookies = document.cookie ? document.cookie.split('; ') : [];
	    for (var i = 0, l = cookies.length; i < l; i++) {
	        var parts = cookies[i].split('=');
	        var name = parts.shift();
	        var cookie = parts.join('=');

	        if (c_name && c_name === name) {
	            return cookie;
	        }
	    }
	    return "";
	};
	
	return {
		'set_cookie': set_cookie,
		'get_cookie': get_cookie,
	}
});
