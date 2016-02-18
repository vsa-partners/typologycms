<?

	$conf 		= CI()->CONF['items'][$item['type']];
	$row_class 	= ($item[CI()->id_field] == CI()->current_id) ? 'data current' : 'data';
	
	$title		= !empty(CI()->CONF['access_menu']['title_field']) ? $item[CI()->CONF['access_menu']['title_field']] : $item['title'];

	$status		= ($item['enabled'] == 1) ? 20 : 0;

?>
<? if ($item['type'] != 'root'):

	$module = !empty($item['module']) ? $item['module'] : $this->module;
	
	?>

	<li>
		<div class="<?=$row_class?>">
			<div class="status status_<?=$status?>"></div>
			<a href="<?=CI()->admin_path.$module?>/edit/<?=$item[CI()->id_field]?>"><img src="<?=CI()->asset_path.$conf['icons']['sm']?>" width="10" height="10"/> <?=$title?></a>
		</div>
	</li>

<? endif; ?>

<? if (!empty($item['children']) && count($item['children'])) {
		foreach ($item['children'] as $child) {
			$this->load->view(CI()->menu_builder->item_view, array('item'=>$child));
		}
	}
?>