
	<? if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') : ?>
	
		<input type="hidden" name="fields[queue_date_period]" value="now" />
	
	<? else: ?>

		<div class="page_content_section publish_conrols">
			<div class="page_content_section_inner">		
			
				<div class="publish_column">
					<div class="title">Publish this page:</div>
					<?=form_dropdown('fields[queue_date_period]', $this->CONF['publish_periods'], $fields['queue_date_period'], 'style="width: 100px;" class="toggle_hide" hideif="date" toshow="queue_time_date_control" ')?>
				</div>
	
				<div id="queue_time_date_control">
	
					<div class="publish_column publish_date_column">
						<div class="title">Date:</div>
						<input type="text" class="action_calendar" name="fields[queue_date_day]" id="queue_time_date_control_day" readonly="readonly" title="Date" style="width: 100px;" value="<?=$fields['queue_date_day']?>"/>
					</div>
	
					<div class="publish_column publish_time_column">
						<div class="title">Time:</div>
						<?=form_dropdown('fields[queue_date_time]', $this->CONF['publish_times'], $fields['queue_date_time'], 'style="width: 60px;"')?>
					</div>
	
				</div>
				
			</div>		
		</div>
		
	<? endif; ?>
