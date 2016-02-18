<? $this->load->view('shared/content_header', array('title' => 'Publish Queue'));  ?>


<script language="javascript">

	function deleteJob(queue_id) {
		var delete_path = ADMIN_PATH + 'util/jobs/delete/' + queue_id;
		confirmDelete(delete_path);
	}

</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
	<tr>
		<th>Module</th>
		<th>Module ID</th>
		<th>Title</th>
		<th>Queue Date</th>
		<th>Type</th>
		<th>Approver</th>
		<th width="20">&nbsp;</th>
	</tr>

	<? foreach ($jobs as $job) {
		
		$user = !empty($job['related_approver']['user']) ? $job['related_approver']['user'] : 'System';

		echo '<tr>'	
			.'<td>'.$job['module'].'</td>'
			.'<td>'.$job['module_id'].'</td>'
			.'<td>'.$job['title'].'</td>'
			.'<td>'.date(DATE_DISPLAY_FORMAT, strtotime($job['queue_date'])).'</td>'
			.'<td>'.$job['queue_type'].'</td>'
			.'<td>'.$user.'</td>'
			.'<td><button type="button" class="button button_outline_small" onClick="deleteJob('.$job['queue_id'].')"><span>CANCEL</span></button></td>'
			.'</tr>';

	} ?>
	
	<? if (!count($jobs)) : ?>
		<tr><td colspan="30">(No pending jobs)</td></tr>
	<? endif; ?>
	
</table>
