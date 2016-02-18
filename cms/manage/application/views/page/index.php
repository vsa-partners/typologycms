<?

	$pending_table_class = (count($pending_pages)) ? 'color_pending' : '';

?>
<div class="dashboard">

	<div class="tab_set">
		
		<div class="tab_nav clearfix">
			<ul id="index_tabs">
				<li><a href="#site_status"><span>Site Status</span></a></li>
				<li><a href="#recent_updates"><span>Recent Updates</span></a></li>
			</ul>
		</div>

		<div id="site_status" class="tab_content">

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small <?=$pending_table_class?>">
				<tr>
					<th>Pending Approval</th>
					<th>Update Date</th>
				</tr>
			
				<? foreach ($pending_pages as $item) { ?>
					<tr class="noborder">
						<td><a href="<?=$this->admin_path.$this->module.'/edit/'.$item['page_id']?>"><?=ucwords($item['title'])?></a></td>
						<td width="200"><?=date(DATE_DISPLAY_FORMAT, strtotime($item['update_date']))?></td>
					</tr>		
					<tr>
						<td colspan="3" class="light"><?=$item['path']?></td>
					</tr>
				<? } ?>
			
				<? if (!count($pending_pages)) : ?>
					<tr><td colspan="30">(No pending approvals)</td></tr>
				<? endif; ?>
	
				<tr><th colspan="3"><a href="<?=$this->admin_path.$this->module?>/report/pending">View Report</a></th></tr>
				
			</table>
			
			<br/><br/>

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
				<tr>
					<th colspan="2">Publish Queue</th>
					<th colspan="2">Queue Date</th>
				</tr>
			
				<? foreach ($queue_jobs as $item) { ?>
					<tr>
						<td width="60"><?=ucwords($item['queue_type'])?></td>
						<td>
							<a href="<?=$this->admin_path.$this->module.'/edit/'.$item['module_id']?>">
								<? echo (!empty($item['title']) ? $item['title'] : ($item['module'].':'.$item['module_id'])); ?>
							</a>
						</td>
						<td width="150"><?=$item['queue_date_display']?></td>
						<td width="50"><a href="<?=$this->admin_path?>util/jobs/delete/<?=$item['queue_id']?>">CANCEL</td>
					</tr>		
				<? } ?>
			
				<? if (!count($queue_jobs)) : ?>
					<tr><td colspan="30">(No publish queues)</td></tr>
				<? endif; ?>
				
			</table>

		</div>

		<div id="recent_updates" class="tab_content">

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
			
	</div> <!-- End .tab_set -->

	<script language="javascript">
		new Control.Tabs('index_tabs');
	</script>



	</div>

	<div class="column right">



	</div>

</div>