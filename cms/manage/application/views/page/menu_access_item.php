<?

	$conf 	= CI()->CONF['items'][$item['type']];

	$li_id	= $item[CI()->id_field];

	// Set up classes
	$li_class	= $item['type'];
	$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
	
	$node_class	= 'node';
	$module		= (!empty($conf['module'])) ? $conf['module'] : $this->module;

	if (!empty($item['template_options']['child_edit_style']) && ($item['template_options']['child_edit_style'] == 'list')) {
		$conf['icons']['sm'] = 'img/mini_icons/list_unordered.gif';
	} else if (isset($conf['list_children']) && ($conf['list_children'] == false)) {
		// Yo, you can't open this
	} else if (!count($conf['allowed_children'])) {
		// Yo, this can't contain childs, so get outta here
	} else if (isset($item['children'])) {
		$node_class .= ' node-open';
	} else {
		$node_class .= ' node-closed';
	}
	
	if ($last && ($item['type'] != 'root')) $li_class .= ' last';

	// Set up label
	$row_label = ' ';
	$row_label .= '<a href="'.CI()->admin_path.$item['module'].'/edit/'.$item[CI()->id_field].'"><img src="' . CI()->asset_path . $conf['icons']['sm'] . '" width="10" height="10"/> '.$item['title'].'</a>';

	// Set up action icons
	$row_icons 	= '';
	if (count($conf['allowed_children']) && CI()->authentication->hasPermission('global_create')) {
		$row_icons .= ' <a class="action_btn" href="'.CI()->admin_path.$module.'/create/'.$item[CI()->id_field].'"><img src="' . CI()->asset_path . 'img/tree_nav/node-icon-add.gif" alt="(ADD)" width="10" height="10"/></a>';
	}
	if ($item['type'] == 'section') {
		$row_icons .= ' <a class="action_btn" href="'.CI()->admin_path.$module.'/edit_sort/'.$item[CI()->id_field].'"><img src="' . CI()->asset_path . 'img/tree_nav/node-icon-sort.gif" alt="(SORT)" width="10" height="10"/></a>';
	}


	// if ($item['type'] == 'page_calendar') {
	// 	$row_icons .= ' <a class="action_btn" href="'.CI()->admin_path.'page_calendar/create/'.$item[CI()->id_field].'"><img src="' . CI()->asset_path . 'img/tree_nav/node-icon-add.gif" alt="(ADD)" width="10" height="10"/></a>';
	// }


	// Check if we are displaying this item as inline, if so hide children. 
	// TODO: Add some kind of identification that children are being hidden
	if (!empty($item['template_options']['child_edit_style']) && ($item['template_options']['child_edit_style'] == 'list')) {
		$item['children'] = array();
	}

?>

	<li id="<?=$li_id?>" class="<?=$li_class?>">
		
		<? if (($item['type'] != 'redirect') || (($item['type'] == 'redirect') && ($this->hide_redirects != 1))) : ?>
	
			<div class="<?=$row_class?>">
				<div class="status status_<?=$item['status']?>"></div>
				<div class="icons"><?=$row_icons?></div>
				<? if ($item['type'] != 'root') :?>
					<div class="<?=$node_class?>"></div>
				<? endif; ?>
				
				<div class="label"><?=$row_label?></div>
			</div>

		<? endif; ?>
		
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

					} else if (($item['type'] == 'section') && !empty($item['template_options']['child_edit_style']) && ($item['template_options']['child_edit_style'] == 'list')) {
						// Nothing
					} else if (($item['type'] == 'section')){
						echo '<li class="last"><div class="data"><div class="node"> </div><div class="label"> (No Children)</div></div></li>';
					}

				} else {
					// Empty, Do nothing
				}
			?>
			</ul>
		</div>
	</li>
		
		