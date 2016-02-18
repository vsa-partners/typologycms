<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
	<tr>
		<th>Publish Queue</th>
		<th colspan="2">Publish Date</th>
	</tr>

	<? foreach ($jobs as $job) {
		$del_url = $this->admin_path.'util/jobs/delete/'.$job['queue_id'].'/';
		?>
		<tr>
			<td><?=ucwords($job['queue_type'])?></td>
			<td width="130"><?=date(DATE_DISPLAY_FORMAT, strtotime($job['queue_date']))?></td>
			<td width="80" style="text-align: right;">
				<a href="#CANCEL" onClick="confirmDelete('<?=$del_url?>', 'Are you sure you want to cancel this queue?');">CANCEL</a>
			</td>

		</tr>		
	<? } ?>

	<? if (!count($jobs)) : ?>
		<tr><td colspan="30">(No queues)</td></tr>
	<? endif; ?>
	
</table>

<br/><br/>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
	<tr>
		<th>Versions</th>
		<th width="90">Editor</th>
		<th width="130">Approval Date</th>
		<th colspan="2"	width="200">Publish Date</th>
	</tr>

	<? foreach ($versions as $version) { 
	
		$version_url = $this->admin_path.'page/versions/'.$this->current_id.'/'.$version['version_id'].'/';
	
		?>
		<tr>
			<td><?=$version['title']?></td>
			<td width="90"><?=$version['related_editor']['user']?></td>
			<td width="130"><?=date(DATE_DISPLAY_FORMAT, strtotime($version['approve_date']))?></td>
			<td width="120"><?
			
				if ($version['queue_date'] == EMPTY_DATE) { 
					echo 'Immediately';
				} else {
					echo date(DATE_DISPLAY_FORMAT, strtotime($version['queue_date']));
				}
				?>
			</td>
			<td width="80" style="text-align: right;">
				<a href="#VIEW"  onClick="TNDR.Modal.showFrame('<?=$version_url?>', 600, 450, 'no'); return false;" style="font-weight: bold;">VIEW</a>			
				<a href="#REVERT" onClick="confirmDelete('<?=$version_url?>REVERT', 'Are you sure you want to replace the current working version with this?');">REVERT</a>
			</td>
		</tr>		
	<? } ?>

	<? if (!count($versions)) : ?>
		<tr><td colspan="30">(No previous versions)</td></tr>
	<? endif; ?>
	
</table>