<?


	if (!empty($item['space']) && $item['space']) {
	
		// This is a spacer row
		echo '<li class="spacer"></li>';
		
	} else {

		$conf 		= CI()->CONF['items'][$item['type']];
		$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
		
		$title		= !empty(CI()->CONF['access_menu']['title_field']) ? $item[CI()->CONF['access_menu']['title_field']] : $item['title'];
		
		
		if (($item['type'] != 'root') && (!empty($title))) {
			$module = !empty($item['module']) ? $item['module'] : $this->module;
			
			echo '<li>'
				.'<div class="'.$row_class.'">'
				.'<a href="'.CI()->admin_path.$module.'/edit/'.$item[CI()->id_field].'"><img src="'.CI()->asset_path.$conf['icons']['sm'].'" width="10" height="10"/> '.$title.'</a>'
				.'</div>'
				.'</li>'
				.NL;
		
		}
	}
