<?


	if (!empty($item['space']) && $item['space']) {
	
		// This is a spacer row
		echo '<li class="spacer"></li>';
		
	} else {

		$type		= !empty($item['template_type']) ? $item['template_type'] : $this->module;
		$conf 		= CI()->CONF['items'][$type];
		$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
		$title		= $item['template_title'];
		$module 	= $this->module;
		
		echo '<li>'
			.'<div class="'.$row_class.'">'
			.'<a href="'.CI()->admin_path.$module.'/edit/'.$item[CI()->id_field].'"><img src="'.CI()->asset_path.$conf['icons']['sm'].'" width="10" height="10"/> '.$title.'</a>'
			.'</div>'
			.'</li>'
			.NL;
	}

	
	// CHILDREN

	if (!empty($item['children']) && count($item['children'])) {
		foreach ($item['children'] as $child) {
			$this->load->view(CI()->menu_builder->item_view, array('item'=>$child));
		}
	}