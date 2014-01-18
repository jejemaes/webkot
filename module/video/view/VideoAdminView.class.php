<?php

class VideoAdminView extends AdminView implements iAdminView{


	public function __construct(Template $template, Module $module){
		parent::setTemplate($template);
		parent::setModule($module);
	}

	public function configureTemplate(){

	}

	
	public function page($videos, $message){
		$content .= '<div class="row">';
		$content .= '<div class="col-lg-12">';
		$content .= '<div class="well">';
		
		$content .= '<h3>Les vid&eacute;os</h3>';
		$content .= '<p><strong>Explications : </strong> pour publier une vid&eacute;o, vous devez vous connecter sur Youtube avec les acc&egrave;s Webkot sur gmail (webkot.be@gmail.com) et ajouter la vid&eacute;o via l\'admin de Youtube. Ensuite, il faut flusher le cache APC (en appuyant sur le bouton) pour qu\'elle soit affich&eacute;e sur le site.</p>';
		
		// flush button
		$content .= '<a class="btn btn-primary" href="'.URLUtils::generateURL($this->getModule()->getName(), array('action' => 'flush')).'"><i class="fa fa-refresh"></i> Flush Videos Cache</a>';	
		
		$content .= '<br><br>' . $message;
		
		$content .= '<table class="table table-striped  table-hover tablesorter">';
		$content .= '<thead>
			<tr>
				<th>Id</th>
				<th>Titre</th>
				<th>URL Youtube</th>
				<th>Description</th>
				<th>URL Thumbnail</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Id</th>
				<th>Titre</th>
				<th>URL Youtube</th>
				<th>Description</th>
				<th>URL Thumbnail</th>
			</tr>
		</tfoot>';
		$content .= '<tbody>';
		foreach ($videos as $v){
			$content .= '<tr>';
			$content .= '<td>'.$v->getId().'</td>';
			$content .= '<td>'.$v->getTitle().'</td><td><a href="http://www.youtube.com/watch?v='.$v->getId().'" target="_blank">http://www.youtube.com/watch?v='.$v->getId().'</a></td><td>'.($v->getDescription()).'</td><td>'.($v->getThumbnail()).'</td>';
			$content .= '</tr>';
		}
		$content .= '</tbody></table>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		
		$t = $this->getTemplate();
		$t->setContent($content);
	}
	
	
}