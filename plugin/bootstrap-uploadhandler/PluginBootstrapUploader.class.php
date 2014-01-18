<?php


class PluginBootstrapUploader extends Plugin implements iPlugin{
	
	 
	public function __construct(array $options = array()){
		$this->setOptions($options);
	}
	
	
	public function load(){
		//js Code Footer
		$jsCodeF1 = '<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
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
		$this->getOptions()["template"]->addJSFooter($jsCodeF1);
		$jsCodeF2 = '<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
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
		$this->getOptions()["template"]->addJSFooter($jsCodeF2);
		$jsCodeF3 = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation 
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>-->
<!-- blueimp Gallery script -->
<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="'. DIR_PLUGIN . 'bootstrap-uploadhandler/js/jquery.fileupload-ui.js"></script>';
		$this->getOptions()["template"]->addJSFooter($jsCodeF3);
		
		
		$jsCodeF4 = "<!-- The main application script -->
<script type=\"text/javascript\" charset=\"utf-8\">

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '".$this->getOptions()['url']."'
    });
    
    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
    // The directory input, doesn't have to be part of the upload form:
    //var input = $('#input');
    data.formData = {directory: '".$this->getOptions()['directory']."', module: '".$this->getOptions()['module']."'};
    if (!data.formData.directory) {
      //input.focus();
      return false;
    }
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
    		
    // Load existing files:
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {result: result});
    });


});

</script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src=\"".DIR_PLUGIN."bootstrap-uploadhandler/js/cors/jquery.xdr-transport.js\"></script>
<![endif]-->";
		$this->getOptions()["template"]->addJSFooter($jsCodeF4);
		
		
		
		$style = '<!-- Generic page styles -->
<link rel="stylesheet" href="'. DIR_PLUGIN . 'bootstrap-uploadhandler/css/style.css">
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="'. DIR_PLUGIN . 'bootstrap-uploadhandler/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="'. DIR_PLUGIN . 'bootstrap-uploadhandler/css/jquery.fileupload-ui-noscript.css"></noscript>';
		$this->getOptions()["template"]->addStyle($style);
		
	}
	
}