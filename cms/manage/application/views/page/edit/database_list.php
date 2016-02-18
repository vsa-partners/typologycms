		<div id="tab_children" class="tab_content">
			
			<button type="button" class="button button_small" onclick="document.location = this.getAttribute('href'); this.blur();" href="<?=$this->admin_path.'page/create/'.$fields[$this->id_field]?>"><span>ADD RECORD</span></button>
			<br/><br/>					

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
				<tr>
					<th>&nbsp;</th>
					<th>Title</th>
					<th>Date Updated</th>
					<th width="10">&nbsp;</th>
				</tr>
				
				<? foreach ($fields['children'] as $row):

					$icon = '<img src="' . CI()->asset_path . $this->CONF['items'][$row['type']]['icons']['sm'] . '" width="10" height="10"/> ';
					?>
			
					<tr id="item_<?=$row[$this->id_field]?>">
						<td width="10"><?=$icon?></td>
						<td><a href="<?=$this->admin_path.$this->module.'/edit/'.$row[$this->id_field]?>"><?=$row['title']?></a></td>
						<td width="130"><?=date(DATE_DISPLAY_FORMAT, strtotime($row['update_date']))?></td>
						<td width="10"><div class="status status_<?=$row['status']?>"><?=$row['status']?></div></td>
					</tr>
				<? endforeach; ?>

				<? if (!count($fields['children'])) : ?>
					<tr><td colspan="30">(No Children)</td></tr>
				<? endif; ?>
				
			</table>

		</div>