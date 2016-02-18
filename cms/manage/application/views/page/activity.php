<? $this->load->view('shared/content_header', array('title' => 'Activity History', 'buttons' => array('back'))); ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr>
		<th>User</th>
		<th>Action</th>
		<th>Date</th>
	</tr>

	<? foreach ($activity as $row) {?>
		
		<tr>
			<td>
				<? if (!empty($row['related_user']['user'])) {
					echo $row['related_user']['user'];
				} else {
					echo 'Unknown User';
				}?>
			</td>
			<td><?=$row['description']?></td>
			<td><?=date(DATE_DISPLAY_FORMAT, strtotime($row['create_date']))?></td>
		</tr>

	<? } ?>
	
	<? if (!count($activity)) : ?>
		<tr><td colspan="3">(No activity logged)</td></tr>
	<? endif; ?>

	
</table>


