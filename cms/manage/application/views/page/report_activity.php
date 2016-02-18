<div class="content_header">
	<h2>Recently Updated Pages</h2>
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr>
		<th>ID</th>
		<th>Path</th>
		<th>User</th>
		<th>Date Updated</th>
		<th width="20">&nbsp;</th>
	</tr>
	
	<? foreach ($data as $row):
	
		$link = ($row['status'] == 99) ? $row[$this->id_field] : '<a href="'.$this->admin_path.$this->module.'/edit/'.$row[$this->id_field].'">'.$row[$this->id_field].'</a>';
		?>

		<tr id="item_<?=$row[$this->id_field]?>">
			<td><?=$link?></td>
			<td><?=$row['path']?></td>
			<td><?=$row['user']?></td>
			<td><a href="<?=$this->admin_path.$this->module?>/activity/<?=$row[$this->id_field]?>"><? if(!is_null($row['update_date'])) echo date(DATE_DISPLAY_FORMAT, strtotime($row['update_date'])); ?></a></td>
			<td><div class="status status_<?=$row['status']?>"><?=$row['status']?></div></td>
		</tr>
	<? endforeach; ?>
	
</table>

<?
//pr($data, 'Report Data'); 
?>