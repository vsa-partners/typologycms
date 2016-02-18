<div class="content_header">
	<h2>Content Import</h2>
</div>
	
<p><strong>Import complete.</strong> The following pages were updated:</p>
<br/>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table">
	<tr>
		<th width="100">Page Id</th>
		<th width="200">Title</th>
		<th>Path</th>
	</tr>
	
	<? foreach ($results as $row => $result): ?>
	
		<tr id="item_1">
			<td style="line-height: 16px"><?=$result['page_id']?></td>
			<td style="line-height: 16px"><?=$result['title']?></td>
			<td style="line-height: 16px"><a href="/manage/page/edit/<?=$result['page_id']?>"><?=$result['path']?></a></td>
		</tr>
	
	<? endforeach; ?>
		
</table>

<br/><br/>

<form id="editForm" class="tndr_form" accept-charset="utf-8" action="#" name="editForm" method="post">

	<div class="clear"> </div>
	<fieldset class="closed" id="set_debug">
		<legend><a href="#" onClick="TNDR.Form.Actions.toggleFieldset('set_debug'); return false;">Updated Row Ids</a></legend>
		<div class="form_row">

			<table width="200" cellspacing="0" cellpadding="0" border="0" class="data_table">
				<? foreach ($updated_rows as $row => $result): ?>
					<tr id="item_1">
						<td style="line-height: 16px"><?=$result[0]?></td>
					</tr>	
				<? endforeach; ?>
			</table>

		</div>
	</fieldset>

</form>


<div class="clear"> </div>