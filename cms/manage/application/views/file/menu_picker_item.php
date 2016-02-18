<?

	$conf 		= CI()->CONF['items'][$item['type']];
	$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
	
	$js_param = array(
		'id'				=> $item[$this->id_field]
		, 'title'			=> str_replace("'","", $item['title'])
		, 'destination'		=> $this->input->get_post('destination')
		, 'update_fields'	=> array(
			'file_path'			=> !empty($item['view_path']) ? $item['view_path'] : ''
			, 'file_title'		=> str_replace("'","", $item['title'])
			, 'file_parent_id'	=> $item['parent_id']
			, 'image_height'	=> (!empty($item['options']['image_height']) ? $item['options']['image_height'] : '')
			, 'image_width'		=> (!empty($item['options']['image_width']) ? $item['options']['image_width'] : '')
			, 'file_parent_id'	=> $item['parent_id']
			)
		);

	if ($this->input->get_post('submitForm')) $js_param['submitForm'] = CI()->input->get_post('submitForm');
	
	$row_icon = '';
	if ($item['is_image']) {
		$row_icon .= '<a href="#PREVIEW" class="img_icon" onClick=\'javascript:itemPicker.togglePreview(this); return false;\'><img src="'.CI()->asset_path . 'img/mini_icons/magnify.gif"/></a>';
	}
	
	if ($this->menu_builder->checkSelectType($item['type'], 'file')) {
		$row_icon 	.= '<a href="#" onClick=\'javascript:itemPicker.choose('.json_encode($js_param).'); return false;\'><img src="'.$this->asset_path.'img/button_pick_sm.gif" alt="PICK" /></a>';
	}
	
	$row_title	= ($item['type'] == 'file') ? $item['title'] : '<a href="'.reduce_double_slashes($this->admin_path.'/'.$this->module.'/picker').'?parent_id='.$item[$this->id_field].'&destination='.$this->input->get_post('destination').'&multi='.$this->input->get_post('multi').'&'.$this->menu_builder->getSelectType(true).'">'.$item['title'].'</a>';
	?>

<? if ($item['type'] != 'root'):

	$module = !empty($item['module']) ? $item['module'] : $this->module;
	
	?>

	<li id="file_<?=$item[$this->id_field]?>">
		<div class="<?=$row_class?>">
			<div class="icons"><?=$row_icon?></div>
			<div class="label"><img src="<?=CI()->asset_path.$conf['icons']['sm']?>" width="10" height="10"/> <?=$row_title?></div>
			<div class="preview" style="display: none;">
				
				<? 
					if ($item['is_image']) {
						echo '<div class="details">'
							.'ID: '.$item['file_id']
							.'<br/>Width: '.$item['options']['image_width'].'px'
							.'<br/>Height: '.$item['options']['image_height'].'px'
							.'<br/>Type: '.$item['mime']
							.'</div>';
						echo '<img src="'.$item['manage_path'].'?w=80"/>';
					} else {
						echo 'Unable to preview this file type.';
					} 
				?>
			</div>
		</div>
	</li>

<? endif; ?>

<? if (!empty($item['children']) && count($item['children'])) {
		foreach ($item['children'] as $child) {
			$this->load->view($this->CI->menu_builder->item_view, array('item'=>$child));
		}
	}
?>