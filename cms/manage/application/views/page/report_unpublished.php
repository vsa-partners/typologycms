<div class="content_header">
	<h2>Draft Pages</h2>
</div>

<form method="post" name="editForm" action="<?=$this->admin_path.$this->module?>/publishPages/id" accept-charset="utf-8">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr>
			<th> </th>
			<th>ID</th>
			<th>Path</th>
			<th>Last Updated</th>
			<th width="20">&nbsp;</th>
		</tr>
		
		<? foreach ($data as $row): 
		
			$link = ($row['status'] == 99) ? $row[$this->id_field] : '<a href="'.$this->admin_path.$this->module.'/edit/'.$row[$this->id_field].'">'.$row[$this->id_field].'</a>';
		
			?>
	
			<tr id="item_<?=$row[$this->id_field]?>">
				<td><input name="id[]" value="<?=$row[$this->id_field]?>" type="checkbox" CHECKED style="margin: 0;" /></td>
				<td><?=$link?></td>
				<td><?=$row['path']?></td>
				<td><? if(!is_null($row['update_date'])) echo date(DATE_DISPLAY_FORMAT, strtotime($row['update_date'])); ?></td>
				<td><div class="status status_<?=$row['status']?>"><?=$row['status']?></div></td>
			</tr>
		<? endforeach; ?>
		
		<tr><th colspan="5"><a href="#UNCHECK" onClick="$$('INPUT[type=checkbox]').invoke('writeAttribute','checked',null); return false;">Uncheck All</a></th></tr>

	</table>
	
	<br/>
	
	<button name="submit_publish" value="uppublish_date" type="submit" class="button"><span>PUBLISH SELECTED</span></button>

</form>
