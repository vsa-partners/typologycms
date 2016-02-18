<?
$this->load->view('shared/content_header', array('title' => 'DB : TABLE : '.$this->current_table)); 

echo '<div style="height: 35px;">'
	.'<button type="button" onClick="confirmDelete(\''.$this->admin_path.$this->module_path.'/'.$this->current_table.'/truncate/\'); return false;" class="button button_outline"><span>EMPTY TABLE</span></button>'
	.'<button type="button"	onClick="document.locaiton = this.getAttribute(\'href\')" href="'.$this->admin_path.$this->module_path.'/'.$this->current_table.'/optimize/" class="button button_outline"><span>OPTIMIZE TABLE</span></button>'
	.'<button type="button" onClick="document.locaiton = this.getAttribute(\'href\')" href="'.$this->admin_path.$this->module_path.'/'.$this->current_table.'/repair/" class="button button_outline"><span>REPAIR TABLE</span></button>'
	.'<button type="button" onClick="document.locaiton = this.getAttribute(\'href\')" href="'.$this->admin_path.$this->module_path.'/'.$this->current_table.'/edit/-1" class="button button_outline"><span>INSERT</span></button>'
	.'</div>';

echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table small">'
	.'<thead>'
		.'<tr><td colspan="20">' .count($rows) .' Records</td></tr></thead>';

if (count($rows)) {
	
	// Table Header

	echo '<tr>'
		.'<th width="20">&nbsp;</th>';

	foreach ($rows[0] as $key => $row) echo '<th>'.$key.'</th>';

	echo '</tr>';
	
	// Table Rows
	
	foreach ($rows as $row) {

		echo '<tr>'	
			.'<td width="20">'
			.'<a href="'.$this->admin_path.$this->module_path.'/'.$this->current_table.'/edit/'.$row[$this->id_field].'">EDIT</a>'
			.'</td>';
		
		foreach ($row as $cell) {
			echo '<td>'.$cell.'&nbsp;</td>';
		} 

		echo '</tr>';

	} 

} else {
	echo '<tr><td colspan="30">(No rows)</td></tr>';
}

echo '</table>';
