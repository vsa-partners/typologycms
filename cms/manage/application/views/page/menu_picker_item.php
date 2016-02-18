<?

	$conf 	= CI()->CONF['items'][$item['type']];

	$li_id	= $item[CI()->id_field];

	// Set up classes
	$li_class	= $item['type'];
	$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';

	$node_class	= 'node';

	if (!count($conf['allowed_children'])) {
		// Yo, this can't contain childs, so get outta here
	} else if (isset($item['children'])) {
		$node_class .= ' node-open';
	} else {
		$node_class .= ' node-closed';
	}
	
	if ($last && ($item['type'] != 'root')) $li_class .= ' last';

	// Set up label
	$row_label = '<img src="' . CI()->asset_path . $conf['icons']['sm'] . '" width="10" height="10"/> ';
	$row_label .= $item['title'];

	//if (empty($params['sType']) || !(strpos($params['sType'], '|'.$item['sType'].'|') === FALSE)) {

		$js_param = array(
			'id'				=> $item[$this->id_field]
			, 'title'			=> str_replace("'", "", $item['title'])
			, 'destination'		=> $this->input->get_post('destination')
			, 'update_fields'	=> array(
				'page_path'		=> $item['path']
				, 'value'		=> str_replace("'", "", $item['title'])
				)
			);

		if ($this->input->get_post('submitForm')) $js_param['submitForm'] = CI()->input->get_post('submitForm');
		
		$row_icon = '';
		
		if ($this->menu_builder->checkSelectType($item['type'])) {
			$row_icon = '<a href="#" onClick=\'javascript:itemPicker.choose('.json_encode($js_param).'); return false;\'><img src="'.$this->asset_path.'img/button_pick_sm.gif" alt="PICK" /></a>';
		}
				
?>
	<li id="<?=$li_id?>" class="<?=$li_class?>">
		<div class="<?=$row_class?>">
			<div class="icons"><?=$row_icon?></div>
			<div class="<?=$node_class?>"></div>
			<div class="label"><?=$row_label?></div>
		</div>
		<div class="children">
			<ul>
			<? if (isset($item['children'])) {
			
					if (count($item['children'])) {
						
						$cnt 	= count($item['children']);
						$i		= 1;
				
						foreach ($item['children'] as $child) {

							$params = array(
								'item' 		=> $child
								, 'last'	=> ($i == $cnt) ? true : false
								);
						
							$i++;
						
							$this->load->view(CI()->menu_builder->item_view, $params);
						}
					} else {
						echo '<li class="last"><div class="data"><div class="node"> </div><div class="label"><em> (No Children)</em></div></div></li>';
					}

				} else {
					// Empty, Do nothing
				}
			?>
			</ul>
		</div>
	</li>