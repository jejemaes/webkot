
function pluginMediaPickerModal(targetUrl, inputid){

	if (!($('#plg-mediapicker-modal').length)){
		var modalCode = '<div class="modal fade" id="plg-mediapicker-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 id="plg-mediapicker-modal-label">Choisissez votre m&eacute;dia</h4></div><div class="modal-body" id="plg-mediapicker-modal-body">	<p>loading ...</p> </div><div class="modal-footer"> <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Save & Close</button></div></div></div></div>';
		$('html').append(modalCode);
	}
	
	$.ajax({
		url: targetUrl,
		async: true,
		dataType: 'html',
		type: 'GET',
		data: {id : inputid},
		cache: false,
		success: function (data, textStatus, xhr) {
			$('#plg-mediapicker-modal-body').html(data);
		},
		error: function (xhr, textStatus, errorThrown) {
			$('#plg-mediapicker-modal-body').html('opps: ' + textStatus + ' : ' + errorThrown);
		}
	});
	
	$('#plg-mediapicker-modal').modal('show');
	
}


function mediapickerChoose(id, mid, filepath){
	$('.btn-mediapicker').removeClass('btn-success');
	$('#btn-mediapicker-'+mid).addClass('btn-success');
	$('#'+id).val(filepath);
	$('#plg-mediapicker-modal').modal('hide');
}