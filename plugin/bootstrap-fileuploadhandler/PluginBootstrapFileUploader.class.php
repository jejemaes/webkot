<?php


class PluginBootstrapFileUploader extends Plugin implements iPlugin{
	
	 
	public function __construct(array $options = array()){
		$default = array (
				"url" => "",
				"template" => null
		);
		
		$tmp = array_merge($default, $options);
		$this->setOptions($tmp);
	}
	
	
	public function load(){
		
		if($this->getOptions()["template"]){
			$template = $this->getOptions()["template"];
			
			// CSS Files
			$css = array('css/jquery.fileupload.css', 'css/jquery.fileupload-ui.css', 'css/style.css');
			for ($i = 0; $i < count($css); $i++) {
				$template->addStyle('<link rel="stylesheet" href="'.DIR_PLUGIN.'bootstrap-fileuploadhandler/'.$css[$i].'">');
			}
			
			// CSS adjustments for browsers with JavaScript disabled 
			$template->addStyle('<noscript><link rel="stylesheet" href="'.DIR_PLUGIN.'bootstrap-fileuploadhandler/css/jquery.fileupload-noscript.css"></noscript>');
			$template->addStyle('<noscript><link rel="stylesheet" href="'.DIR_PLUGIN.'bootstrap-fileuploadhandler/css/jquery.fileupload-ui-noscript.css"></noscript>');

					
					
			// Import JavaScript
			$js = '<script id="template-upload" type="text/x-tmpl">
				{% for (var i=0, file; file=o.files[i]; i++) { %}
				    <tr class="template-upload fade">
				        <td>
				            <span class="preview"></span>
				        </td>
				        <td>
				            <p class="name">{%=file.name%}</p>
				            <strong class="error text-danger"></strong>
				        </td>
				        <td>
				            <p class="size">Processing...</p>
				            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
				        </td>
				        <td>
				            {% if (!i && !o.options.autoUpload) { %}
				                <button class="btn btn-primary start" disabled>
				                    <i class="glyphicon glyphicon-upload"></i>
				                    <span>Start</span>
				                </button>
				            {% } %}
				            {% if (!i) { %}
				                <button class="btn btn-warning cancel">
				                    <i class="glyphicon glyphicon-ban-circle"></i>
				                    <span>Cancel</span>
				                </button>
				            {% } %}
				        </td>
				    </tr>
				{% } %}
				</script>';		
			$template->addJSFooter($js);
			
			$js1 = '<script id="template-download" type="text/x-tmpl">
				{% for (var i=0, file; file=o.files[i]; i++) { %}
				    <tr class="template-download fade">
				        <td>
				            <span class="preview">
				                {% if (file.thumbnailUrl) { %}
				                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
				                {% } %}
				            </span>
				        </td>
				        <td>
				            <p class="name">
				                {% if (file.url) { %}
				                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?\'data-gallery\':\'\'%}>{%=file.name%}</a>
				                {% } else { %}
				                    <span>{%=file.name%}</span>
				                {% } %}
				            </p>
				            {% if (file.error) { %}
				                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
				            {% } %}
				        </td>
				        <td>
				            <span class="size">{%=o.formatFileSize(file.size)%}</span>
				        </td>
				        <td>
				            {% if (file.deleteUrl) { %}
				                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
				                    <i class="glyphicon glyphicon-trash"></i>
				                    <span>Delete</span>
				                </button>
				                <input type="checkbox" name="delete" value="1" class="toggle">
				            {% } else { %}
				                <button class="btn btn-warning cancel">
				                    <i class="glyphicon glyphicon-ban-circle"></i>
				                    <span>Cancel</span>
				                </button>
				            {% } %}
				        </td>
				    </tr>
				{% } %}
				</script>';
			$template->addJSFooter($js1);
			/*
			$online = array(
					'//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js',
					'//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js',
					'//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js',
					'//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js',
			);
			*/
			$scripts = array(
				// other lib, must be loaded first !
				'js/other_lib/tmpl.min.js',
				'js/other_lib/load-image.all.min.js',
				'js/other_lib/canvas-to-blob.min.js',
				'js/other_lib/jquery.blueimp-gallery.min.js',
				// plugin js
				'js/vendor/jquery.ui.widget.js', 
				'js/jquery.iframe-transport.js',
				'js/jquery.fileupload.js',
				'js/jquery.fileupload-process.js',
				'js/jquery.fileupload-image.js',
				'js/jquery.fileupload-audio.js',
				'js/jquery.fileupload-video.js',
				'js/jquery.fileupload-validate.js',
				'js/jquery.fileupload-ui.js',
				//'js/main.js'
					
			);
			
			for ($i = 0; $i < count($scripts); $i++) {
				$template->addJSFooter('<script src="'.DIR_PLUGIN.'bootstrap-fileuploadhandler/'.$scripts[$i].'"></script>');
			}
		
			
			$js2 = "<script>
				$(function () {
				    'use strict';
				
				    // Initialize the jQuery File Upload widget:
				    $('#fileupload').fileupload({
				        // Uncomment the following to send cross-domain cookies:
				        //xhrFields: {withCredentials: true},
				        url: '".$this->getOptions()["url"]."'
				    });
				
				    // Enable iframe cross-domain access via redirect option:
				    $('#fileupload').fileupload(
				        'option',
				        'redirect',
				        window.location.href.replace(
				            /\/[^\/]*$/,
				            '/cors/result.html?%s'
				        )
				    );
				
				    if (window.location.hostname === 'blueimp.github.io') {
				        // Demo settings:
				        $('#fileupload').fileupload('option', {
				            url: '//jquery-file-upload.appspot.com/',
				            // Enable image resizing, except for Android and Opera,
				            // which actually support image resizing, but fail to
				            // send Blob objects via XHR requests:
				            disableImageResize: /Android(?!.*Chrome)|Opera/
				                .test(window.navigator.userAgent),
				            maxFileSize: 5000000,
				            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
				        });
				        // Upload server status check for browsers with CORS support:
				        if ($.support.cors) {
				            $.ajax({
				                url: '//jquery-file-upload.appspot.com/',
				                type: 'HEAD'
				            }).fail(function () {
				                $('<div class=\"alert alert-danger\"/>')
				                    .text('Upload server currently unavailable - ' +
				                            new Date())
				                    .appendTo('#fileupload');
				            });
				        }
				    } else {
				        // Load existing files:
				        $('#fileupload').addClass('fileupload-processing');
				        $.ajax({
				            // Uncomment the following to send cross-domain cookies:
				            //xhrFields: {withCredentials: true},
				            url: $('#fileupload').fileupload('option', 'url'),
				            dataType: 'json',
				            context: $('#fileupload')[0]
				        }).always(function () {
				            $(this).removeClass('fileupload-processing');
				        }).done(function (result) {
				            $(this).fileupload('option', 'done')
				                .call(this, $.Event('done'), {result: result});
				        });
				    }
				
				});</script>";
			$template->addJSFooter($js2);
				
		}
		
		
		$html = '<form id="fileupload" action="'.$this->getOptions()["url"].'" method="POST" enctype="multipart/form-data">
	        <!-- Redirect browsers with JavaScript disabled to the origin page -->
	        <noscript><input type="hidden" name="redirect" value="'.$this->getOptions()["url"].'"></noscript>
	        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	        <div class="row fileupload-buttonbar">
	            <div class="col-lg-7">
	                <!-- The fileinput-button span is used to style the file input field as button -->
	                <span class="btn btn-success fileinput-button">
	                    <i class="glyphicon glyphicon-plus"></i>
	                    <span>Add files...</span>
	                    <input type="file" name="files[]" multiple>
	                </span>
	                <button type="submit" class="btn btn-primary start">
	                    <i class="glyphicon glyphicon-upload"></i>
	                    <span>Start upload</span>
	                </button>
	                <button type="reset" class="btn btn-warning cancel">
	                    <i class="glyphicon glyphicon-ban-circle"></i>
	                    <span>Cancel upload</span>
	                </button>
	                <button type="button" class="btn btn-danger delete">
	                    <i class="glyphicon glyphicon-trash"></i>
	                    <span>Delete</span>
	                </button>
	                <input type="checkbox" class="toggle">
	                <!-- The global file processing state -->
	                <span class="fileupload-process"></span>
	            </div>
	            <!-- The global progress state -->
	            <div class="col-lg-5 fileupload-progress fade">
	                <!-- The global progress bar -->
	                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
	                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
	                </div>
	                <!-- The extended global progress state -->
	                <div class="progress-extended">&nbsp;</div>
	            </div>
	        </div>
	        <!-- The table listing the files available for upload/download -->
	        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
	    </form>';
		
		/*
		$html = '<!-- The file upload form used as target for the file upload widget -->
		    <form id="fileupload" action="'.$this->getOptions()["url"].'" method="POST" enctype="multipart/form-data">
		        <!-- Redirect browsers with JavaScript disabled to the origin page -->
		        <noscript><input type="hidden" name="redirect" value="'.$this->getOptions()["url"].'"></noscript>
		        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
		        <div class="row fileupload-buttonbar">
		            <div class="col-lg-7">
		                <!-- The fileinput-button span is used to style the file input field as button -->
		                <span class="btn btn-success fileinput-button">
		                    <i class="glyphicon glyphicon-plus"></i>
		                    <span>Add files...</span>
		                    <input type="file" name="files[]" multiple>
		                </span>
		                <button type="submit" class="btn btn-primary start">
		                    <i class="glyphicon glyphicon-upload"></i>
		                    <span>Start upload</span>
		                </button>
		                <button type="reset" class="btn btn-warning cancel">
		                    <i class="glyphicon glyphicon-ban-circle"></i>
		                    <span>Cancel upload</span>
		                </button>
		                <button type="button" class="btn btn-danger delete">
		                    <i class="glyphicon glyphicon-trash"></i>
		                    <span>Delete</span>
		                </button>
		                <input type="checkbox" class="toggle">
		                <!-- The global file processing state -->
		                <span class="fileupload-process"></span>
		            </div>
		            <!-- The global progress state -->
		            <div class="col-lg-5 fileupload-progress fade">
		                <!-- The global progress bar -->
		                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
		                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
		                </div>
		                <!-- The extended global progress state -->
		                <div class="progress-extended">&nbsp;</div>
		            </div>
		        </div>
		        <!-- The table listing the files available for upload/download -->
		        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
		    </form>';
		
		*/
		
		return $html;
		
	}
	
}