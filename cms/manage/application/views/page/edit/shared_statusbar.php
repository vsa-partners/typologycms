<? 

$status_message = '';
$status_button 	= '';

switch($fields['status']) {
	
	case 1:
		// Draft

		$status_message = 'The status of this page is <strong>'.$this->CONF['status'][$fields['status']]['title'] . '</strong>.';

		if (!empty($fields['publish_date']) && ($fields['publish_date'] != '0000-00-00 00:00:00')) {
			$status_message .= '<br/>It was last published on '.date(DATE_DISPLAY_FORMAT, strtotime($fields['publish_date']));
		} else {
			$status_message .= '<br/>It has never been published.';
		}
		
		break;
	case 5:
		// Decline

		$status_message = 'The status of this page is <strong>'.$this->CONF['status'][$fields['status']]['title'] . '</strong>.';
		
		break;
	case 10:
		// Pending Approval
		
		$status_message = 'The status of this page is <strong>'.$this->CONF['status'][$fields['status']]['title']. '</strong>. <br/> It was last updated on '.date(DATE_DISPLAY_FORMAT, strtotime($fields['update_date']));
		
		if ($this->authentication->hasPermission('global_publish')) {
			$status_button = '<button name="submit_publish" value="update_publish" type="submit" class="button button_small"><span>APPROVE</span></button>'
							.'<button name="submit_decline" value="update_decline" type="submit" class="button button_small"><span>DELINE</span></button>';
		}

		break;
	case 20:
		// Published

		$status_message = 'The status of this page is <strong>'.$this->CONF['status'][$fields['status']]['title']. '</strong>.';
		
		if ($fields['queue_date_period'] == 'now') {
			$status_message .= '<br/>Approved and queued on ' . date(DATE_DISPLAY_FORMAT, strtotime($fields['approve_date'])) . '.';
		} else {
			$status_message .= '<br/>Approved on ' 
							. date(DATE_DISPLAY_FORMAT, strtotime($fields['approve_date'])) 
							. ' to be published ' 
							. date(DATE_DISPLAY_FORMAT, strtotime($fields['queue_date_day'] . ' ' . $fields['queue_date_time']))
							. '.';
		}

//		$status_button = '<button name="submit_unpublish" value="update_unpublish" type="button" class="button button_small"><span>UNPUBLISH</span></button>';
		
		break;
	case 90:
		// Deleted 
		
		break;

} ?>


<div class="statusbar statusbar_<?=$fields['status']?>">

	<div class="statusbar_icon"></div>
	<div class="statusbar_text">
		<?=$status_message?>	
	</div>
	<div class="statusbar_button">
		<?=$status_button?>	
	</div>

</div>