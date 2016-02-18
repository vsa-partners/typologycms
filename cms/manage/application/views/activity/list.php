<? $this->load->view('shared/content_header', array('title' => 'Activity History', 'buttons' => array('back'))); ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">
	<tr>
		<th width="70">Module</th>
		<th width="40">Id</th>
		<th>User</th>
		<th>Action</th>
		<th width="200">Date</th>
	</tr>

	<? foreach ($items as $row) {
	
		if ($this->module == 'activity') {
	
			echo '<tr>'
				.'<td><a href="'.$this->activity_link.$row['module'].'">'.$row['module'].'</a></td>'
				.'<td><a href="'.$this->activity_link.$row['module'].'/'.$row['module_id'].'">'.$row['module_id'].'</a></td>'
				.'<td><a href="'.$this->activity_link.'by/'.$row['user_id'].'">'.$row['user'].'</a></td>'
				.'<td style="font-weight: bold;">'.$row['description'].'</td>'
				.'<td>'.date(DATE_DISPLAY_FORMAT, strtotime($row['date'])).'</td>'
				.'</tr>'
				. NL;
			
		} else {

			// Displaying results inside another module (like page activity) so dont draw filter links

			echo '<tr>'
				.'<td>'.$row['module'].'</td>'
				.'<td>'.$row['module_id'].'</td>'
				.'<td>'.$row['user'].'</td>'
				.'<td style="font-weight: bold;">'.$row['description'].'</td>'
				.'<td>'.date(DATE_DISPLAY_FORMAT, strtotime($row['date'])).'</td>'
				.'</tr>'
				. NL;		
		
		}
			
	} ?>
	
	<? if (!count($items)) : ?>
		<tr><td colspan="30">(No activity logged)</td></tr>
	<? endif; ?>
	
</table>
