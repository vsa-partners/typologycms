<?

	$title		= (!empty($item['title']) ? $item['title'] : $item[$this->module.'_title']);
	$type		= (!empty($item['type']) ? $item['type'] : $item[$this->module.'_type']);

	$conf 		= CI()->CONF['items'][$type];
	$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
	

	$js_param = array(
		'id'			=> $item[$this->id_field]
		, 'title'		=> $title
		, 'destination'	=> $this->input->get_post('destination')
		);
	
	if ($this->input->get_post('submitForm')) $js_param['submitForm'] = CI()->input->get_post('submitForm');
	
	if ($this->menu_builder->checkSelectType($type)) {
		$row_icon = '<a href="#" onClick=\'javascript:itemPicker.choose('.json_encode($js_param).'); return false;\'><img src="'.$this->asset_path.'img/button_pick_sm.gif" alt="PICK" /></a>';
	}
	

?>
<? if ($type != 'root'):

	$module = !empty($item['module']) ? $item['module'] : $this->module;
	
	?>

	<li>
		<div class="<?=$row_class?>">
			<div class="icons"><?=$row_icon?></div>
			<div class="label">
				<img src="<?=CI()->asset_path.$conf['icons']['sm']?>" width="10" height="10"/> <?=$title?>
			</div>
		</div>
	</li>

<? endif; ?>

<? if (!empty($item['children']) && count($item['children'])) {
		foreach ($item['children'] as $child) {
			$this->load->view(CI()->menu_builder->item_view, array('item' => $child));
		}
	}
?>