<div class="content_header">
	<h2>Content Import</h2>
</div>
<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/execute'?>" accept-charset="utf-8" enctype="multipart/form-data">
	
	<p>The following changes will be made to content as part of the import process. Are you sure you want to proceed?</p>
	<br/>
	
	<input type="hidden" name="tmp_name" value="<?=$tmp_name?>" />
	<input type="hidden" name="settings[method]" value="<?=$settings['method']?>" />
	<input type="hidden" name="settings[template_id]" value="<?=$settings['template_id']?>" />
	<input type="hidden" name="settings[parent_id]" value="<?=$settings['parent_id']?>" />

	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table">
		<tr>
			<th width="100">Row</th>
			<th width="100">Page</th>
			<th>Results</th>
		</tr>
		
		<? foreach ($results as $row => $result): ?>
		
			<tr id="item_1">
				<td style="line-height: 16px"><?=$row+1?></td>
				<td style="line-height: 16px">
					<? echo ($result['page_id'] > 0) ? $result['page_id'] : 'Create'; ?>
				</td>
				<td style="line-height: 16px"><?=implode('<br/>', $result['result'])?></td>
			</tr>
		
		<? endforeach; ?>
			
	</table>
	
	<div class="form_row">
		<div class="field" style="padding: 10px 0 0 0;"><button type="submit" class="button"><span>Execute</span></button></div>
	</div>
	
</form>

<div class="clear"> </div>