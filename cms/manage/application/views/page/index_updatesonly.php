<?

	$pending_table_class = (count($pending_pages)) ? 'color_pending' : '';

?>
<div class="dashboard">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
		<tr>
			<th>Recent Updates</th>
			<th colspan="3">Update Date</th>
		</tr>
	
		<? 
		
		$i=0;
		foreach ($updated_pages as $item) {
			$i++;			
			?>
			<tr class="noborder">
				<td><a href="<?=$this->admin_path.$this->module.'/edit/'.$item['page_id']?>"><?=ucwords($item['title'])?></a></td>
				<td width="150"><?=date(DATE_DISPLAY_FORMAT, strtotime($item['update_date']))?></td>
				<td width="40"><?=$item['user']?></td>
				<td width="10"><div class="status status_<?=$item['status']?>"></div></td>
			</tr>		
			<tr id="updatedpages_<?=$i?>">
				<td colspan="4" class="light"><?=$item['path']?></td>
			</tr>
		<? } ?>
	
		<? if (!count($updated_pages)) : ?>
			<tr><td colspan="30">(No recent updates)</td></tr>
		<? endif; ?>
		
	</table>

</div>