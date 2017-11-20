blackcms.define('admin.view_form', function(require){
	
	
	
	$( document ).ready(function() {
		var $forms = $(".bk_admin_view_form");
		if($forms){
			var $many2one = $forms.find("select[data-type='many2one']")
			_.each($many2one, function(item){
				var $elem = $(item);
				var res_model = $elem.data('res-model');
				
				var data = [{ id: 0, text: 'enhancement' }, { id: 1, text: 'bug' }, { id: 2, text: 'duplicate' }, { id: 3, text: 'invalid' }, { id: 4, text: 'wontfix' }];
				console.log($elem);
				$elem.select2({
					  ajax: {
						    url: "http://localhost/Web%20Developpement/Workspace/webkot4/web/name_search/res_user",
						    dataType: 'json',
						    delay: 250,
						    data: function (params) {
						    	console.log('param', params);
						      return {
						        name: params.term, // search term
						        page: params.page
						      };
						    },
						    processResults: function (data, params) {
						    	console.log('param2', params);
						    	console.log('data', data);
						    	
						    	var res = [];
						    	_.each(data, function(item){
						    		res.push({
						    			'id': item[0],
						    			'text': item[1]
						    		});
						    	});
						    	return {
						    		results: res
						    	}
						    	
						      // parse the results into the format expected by Select2
						      // since we are using custom formatting functions we do not need to
						      // alter the remote JSON data, except to indicate that infinite
						      // scrolling can be used
						      params.page = params.page || 1;
						 
						      return {
						        results: data.items,
						        pagination: {
						          more: (params.page * 30) < data.total_count
						        }
						      };
						    },
						    cache: true
					  },
					  theme: "bootstrap",
				});
				$('.select2').removeAttr( "style" );
			});
		}
	});
	
});
